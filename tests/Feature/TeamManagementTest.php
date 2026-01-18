<?php

use App\Enums\TeamRole;
use App\Filament\App\Pages\TeamSettings;
use App\Filament\App\Resources\Users\Pages\ListUsers as AppListUsers;
use App\Models\Team;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\UserInvitationService;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

describe('Admin Panel - Invite User', function () {
    it('invites user as individual via service', function () {
        Notification::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $service = new UserInvitationService;

        $invitation = $service->inviteAsIndividual('newuser@example.com', $admin);

        expect($invitation)->toBeInstanceOf(UserInvitation::class)
            ->and($invitation->email)->toBe('newuser@example.com')
            ->and($invitation->team_id)->toBeNull()
            ->and($invitation->role)->toBeNull()
            ->and($invitation->isForIndividual())->toBeTrue();
    });
});

describe('App Panel - Register Team', function () {
    it('creates team with owner as coach', function () {
        $user = User::factory()->create(['is_admin' => false]);

        $team = Team::create([
            'name' => 'My New Team',
            'slug' => 'my-new-team',
            'is_personal' => false,
            'owner_id' => $user->id,
        ]);

        $team->users()->attach($user, ['role' => TeamRole::Coach->value]);

        expect($team)->not->toBeNull()
            ->and($team->name)->toBe('My New Team')
            ->and($team->is_personal)->toBeFalse()
            ->and($team->owner_id)->toBe($user->id)
            ->and($user->isCoach($team))->toBeTrue();
    });

    it('user can belong to multiple teams', function () {
        $user = User::factory()->create(['is_admin' => false]);

        $team1 = Team::create([
            'name' => 'Team One',
            'slug' => 'team-one',
            'is_personal' => false,
            'owner_id' => $user->id,
        ]);
        $team1->users()->attach($user, ['role' => TeamRole::Coach->value]);

        $team2 = Team::create([
            'name' => 'Team Two',
            'slug' => 'team-two',
            'is_personal' => false,
            'owner_id' => $user->id,
        ]);
        $team2->users()->attach($user, ['role' => TeamRole::Coach->value]);

        expect($user->fresh()->teams)->toHaveCount(3);
    });
});

describe('Team Settings - Leave Team', function () {
    beforeEach(function () {
        $this->team = Team::factory()->create(['is_personal' => false]);
        $this->owner = User::factory()->create(['is_admin' => false]);
        $this->member = User::factory()->create(['is_admin' => false]);

        $this->team->update(['owner_id' => $this->owner->id]);
        $this->team->users()->attach($this->owner, ['role' => TeamRole::Coach->value]);
        $this->team->users()->attach($this->member, ['role' => TeamRole::Client->value]);
    });

    it('owner cannot see leave team action', function () {
        $this->actingAs($this->owner);
        Filament::setTenant($this->team);

        Livewire::test(TeamSettings::class)
            ->assertActionHidden('leaveTeam');
    });

    it('cannot leave personal team', function () {
        $personalTeam = $this->member->personalTeam();

        $this->actingAs($this->member);
        Filament::setTenant($personalTeam);

        Livewire::test(TeamSettings::class)
            ->assertActionHidden('leaveTeam');
    });

    it('user can leave team via direct detach', function () {
        $this->member->teams()->detach($this->team->id);

        expect($this->member->fresh()->teams->contains($this->team))->toBeFalse();
    });
});

describe('Team Settings - Transfer Ownership', function () {
    beforeEach(function () {
        $this->team = Team::factory()->create(['is_personal' => false]);
        $this->owner = User::factory()->create(['is_admin' => false]);
        $this->coach = User::factory()->create(['is_admin' => false]);
        $this->client = User::factory()->create(['is_admin' => false]);

        $this->team->update(['owner_id' => $this->owner->id]);
        $this->team->users()->attach($this->owner, ['role' => TeamRole::Coach->value]);
        $this->team->users()->attach($this->coach, ['role' => TeamRole::Coach->value]);
        $this->team->users()->attach($this->client, ['role' => TeamRole::Client->value]);
    });

    it('owner can transfer ownership to another coach', function () {
        $this->actingAs($this->owner);
        Filament::setTenant($this->team);

        Livewire::test(TeamSettings::class)
            ->assertActionVisible('transferOwnership')
            ->callAction('transferOwnership', [
                'new_owner_id' => $this->coach->id,
            ])
            ->assertNotified('Ownership transferred');

        expect($this->team->fresh()->owner_id)->toBe($this->coach->id);
    });

    it('non-owner cannot see transfer action', function () {
        $this->actingAs($this->coach);
        Filament::setTenant($this->team);

        Livewire::test(TeamSettings::class)
            ->assertActionHidden('transferOwnership');
    });

    it('cannot transfer ownership on personal team', function () {
        $personalTeam = $this->owner->personalTeam();

        $this->actingAs($this->owner);
        Filament::setTenant($personalTeam);

        Livewire::test(TeamSettings::class)
            ->assertActionHidden('transferOwnership');
    });
});

describe('Team Members - Remove User', function () {
    beforeEach(function () {
        $this->team = Team::factory()->create(['is_personal' => false]);
        $this->owner = User::factory()->create(['is_admin' => false]);
        $this->member = User::factory()->create(['is_admin' => false]);

        $this->team->update(['owner_id' => $this->owner->id]);
        $this->team->users()->attach($this->owner, ['role' => TeamRole::Coach->value]);
        $this->team->users()->attach($this->member, ['role' => TeamRole::Client->value]);
    });

    it('owner can remove member from team', function () {
        $this->actingAs($this->owner);
        Filament::setTenant($this->team);

        Livewire::test(AppListUsers::class)
            ->assertTableActionVisible('remove', $this->member)
            ->callTableAction('remove', $this->member)
            ->assertNotified('User removed');

        expect($this->member->fresh()->teams->contains($this->team))->toBeFalse();
    });

    it('owner cannot remove themselves', function () {
        $this->actingAs($this->owner);
        Filament::setTenant($this->team);

        Livewire::test(AppListUsers::class)
            ->assertTableActionHidden('remove', $this->owner);
    });

    it('cannot remove users from personal team', function () {
        $personalTeam = $this->owner->personalTeam();

        $this->actingAs($this->owner);
        Filament::setTenant($personalTeam);

        Livewire::test(AppListUsers::class)
            ->assertTableActionHidden('remove', $this->owner);
    });
});

describe('Personal Team Auto-Creation', function () {
    it('creates personal team when new user accepts individual invitation', function () {
        $inviter = User::factory()->create(['is_admin' => true]);

        $service = new UserInvitationService;
        $invitation = $service->inviteAsIndividual('newperson@example.com', $inviter);

        $user = $service->acceptInvitation($invitation, 'password123');

        expect($user->hasPersonalTeam())->toBeTrue()
            ->and($user->personalTeam()->is_personal)->toBeTrue()
            ->and($user->personalTeam()->owner_id)->toBe($user->id)
            ->and($user->isCoach($user->personalTeam()))->toBeTrue();
    });

    it('creates personal team when user is created directly', function () {
        $user = User::factory()->create(['is_admin' => false]);

        expect($user->hasPersonalTeam())->toBeTrue()
            ->and($user->personalTeam()->is_personal)->toBeTrue();
    });

    it('does not create personal team for admin users', function () {
        $admin = User::factory()->create(['is_admin' => true]);

        expect($admin->hasPersonalTeam())->toBeFalse();
    });
});
