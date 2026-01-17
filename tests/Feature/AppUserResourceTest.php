<?php

use App\Enums\TeamRole;
use App\Filament\App\Resources\Users\Pages\EditUser;
use App\Filament\App\Resources\Users\Pages\ListUsers;
use App\Models\Team;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('app'));
    $this->team = Team::factory()->create(['is_personal' => false]);
});

describe('Personal team auto-creation', function () {
    it('creates personal team for new non-admin users', function () {
        $user = User::factory()->create(['is_admin' => false, 'name' => 'John Doe']);

        expect($user->hasPersonalTeam())->toBeTrue()
            ->and($user->personalTeam()->is_personal)->toBeTrue()
            ->and($user->personalTeam()->owner_id)->toBe($user->id)
            ->and($user->isCoach($user->personalTeam()))->toBeTrue();
    });

    it('does not create personal team for system admins', function () {
        $admin = User::factory()->create(['is_admin' => true]);

        expect($admin->hasPersonalTeam())->toBeFalse();
    });

    it('handles duplicate slugs for personal teams', function () {
        $user1 = User::factory()->create(['is_admin' => false, 'name' => 'John Doe']);
        $user2 = User::factory()->create(['is_admin' => false, 'name' => 'John Doe']);

        expect($user1->personalTeam()->slug)->not->toBe($user2->personalTeam()->slug);
    });
});

describe('Team role isolation', function () {
    it('user can have different roles in different teams', function () {
        $user = User::factory()->create(['is_admin' => false]);
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();

        $user->teams()->attach($team1, ['role' => TeamRole::Coach]);
        $user->teams()->attach($team2, ['role' => TeamRole::Client]);

        expect($user->isCoach($team1))->toBeTrue()
            ->and($user->isClient($team2))->toBeTrue()
            ->and($user->isClient($team1))->toBeFalse()
            ->and($user->isCoach($team2))->toBeFalse();
    });

    it('role change in one team does not affect other teams', function () {
        $user = User::factory()->create(['is_admin' => false]);
        $team1 = Team::factory()->create();
        $team2 = Team::factory()->create();

        $user->teams()->attach($team1, ['role' => TeamRole::Coach]);
        $user->teams()->attach($team2, ['role' => TeamRole::Coach]);

        $user->teams()->updateExistingPivot($team1->id, ['role' => TeamRole::Client]);

        expect($user->isClient($team1))->toBeTrue()
            ->and($user->isCoach($team2))->toBeTrue();
    });
});

describe('UserResource access control', function () {
    it('coaches can view team members list', function () {
        $coach = User::factory()->create(['is_admin' => false]);
        $coach->teams()->attach($this->team, ['role' => TeamRole::Coach]);

        $this->actingAs($coach);
        Filament::setTenant($this->team);

        Livewire::test(ListUsers::class)
            ->assertSuccessful();
    });

    it('clients cannot view team members list', function () {
        $client = User::factory()->create(['is_admin' => false]);
        $client->teams()->attach($this->team, ['role' => TeamRole::Client]);

        $this->actingAs($client);
        Filament::setTenant($this->team);

        Livewire::test(ListUsers::class)
            ->assertForbidden();
    });

    it('coaches can only see users in their team', function () {
        $coach = User::factory()->create(['is_admin' => false]);
        $coach->teams()->attach($this->team, ['role' => TeamRole::Coach]);

        $teamMember = User::factory()->create(['is_admin' => false]);
        $teamMember->teams()->attach($this->team, ['role' => TeamRole::Client]);

        $otherTeam = Team::factory()->create();
        $otherUser = User::factory()->create(['is_admin' => false]);
        $otherUser->teams()->attach($otherTeam, ['role' => TeamRole::Client]);

        $this->actingAs($coach);
        Filament::setTenant($this->team);

        Livewire::test(ListUsers::class)
            ->assertCanSeeTableRecords([$coach, $teamMember])
            ->assertCanNotSeeTableRecords([$otherUser]);
    });

    it('coaches can edit team member roles', function () {
        $coach = User::factory()->create(['is_admin' => false]);
        $coach->teams()->attach($this->team, ['role' => TeamRole::Coach]);

        $client = User::factory()->create(['is_admin' => false]);
        $client->teams()->attach($this->team, ['role' => TeamRole::Client]);

        $this->actingAs($coach);
        Filament::setTenant($this->team);

        Livewire::test(EditUser::class, ['record' => $client->id])
            ->assertSuccessful()
            ->fillForm(['team_role' => TeamRole::Coach->value])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($client->fresh()->isCoach($this->team))->toBeTrue();
    });
});

describe('TeamRole enum', function () {
    it('only has Coach and Client roles', function () {
        $cases = TeamRole::cases();

        expect($cases)->toHaveCount(2)
            ->and(array_map(fn ($case) => $case->value, $cases))->toBe(['coach', 'client']);
    });
});
