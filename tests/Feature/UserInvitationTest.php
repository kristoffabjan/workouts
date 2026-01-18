<?php

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use App\Models\UserInvitation;
use App\Notifications\UserInvitedNotification;
use App\Services\UserInvitationService;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

beforeEach(function () {
    $this->team = Team::factory()->create(['is_personal' => false]);
    $this->inviter = User::factory()->create(['is_admin' => false]);
    $this->inviter->teams()->attach($this->team, ['role' => TeamRole::Coach]);
});

describe('UserInvitationService', function () {
    it('creates invitation for existing user requiring confirmation', function () {
        Notification::fake();

        $existingUser = User::factory()->create(['is_admin' => false]);
        $service = new UserInvitationService;

        $result = $service->inviteToTeam(
            email: $existingUser->email,
            team: $this->team,
            role: TeamRole::Client,
            inviter: $this->inviter,
        );

        expect($result)->toBeInstanceOf(UserInvitation::class)
            ->and($result->email)->toBe($existingUser->email)
            ->and($result->team_id)->toBe($this->team->id)
            ->and($result->role)->toBe(TeamRole::Client)
            ->and($existingUser->fresh()->teams)->toHaveCount(1); // only personal team, not yet added

        Notification::assertSentOnDemand(UserInvitedNotification::class);
    });

    it('creates invitation for new user', function () {
        Notification::fake();

        $service = new UserInvitationService;
        $email = 'newuser@example.com';

        $result = $service->inviteToTeam(
            email: $email,
            team: $this->team,
            role: TeamRole::Client,
            inviter: $this->inviter,
        );

        expect($result)->toBeInstanceOf(UserInvitation::class)
            ->and($result->email)->toBe($email)
            ->and($result->team_id)->toBe($this->team->id)
            ->and($result->role)->toBe(TeamRole::Client)
            ->and($result->invited_by)->toBe($this->inviter->id)
            ->and($result->token)->toHaveLength(64)
            ->and($result->expires_at)->toBeGreaterThan(now());

        Notification::assertSentOnDemand(UserInvitedNotification::class);
    });

    it('creates individual invitation without team', function () {
        Notification::fake();

        $service = new UserInvitationService;
        $email = 'individual@example.com';

        $result = $service->inviteAsIndividual(
            email: $email,
            inviter: $this->inviter,
        );

        expect($result)->toBeInstanceOf(UserInvitation::class)
            ->and($result->team_id)->toBeNull()
            ->and($result->role)->toBeNull()
            ->and($result->isForIndividual())->toBeTrue();
    });

    it('accepts invitation and creates new user', function () {
        $invitation = UserInvitation::factory()->create([
            'email' => 'newuser@example.com',
            'team_id' => $this->team->id,
            'role' => TeamRole::Client,
            'invited_by' => $this->inviter->id,
            'expires_at' => now()->addDays(7),
        ]);

        $service = new UserInvitationService;
        $user = $service->acceptInvitation($invitation, 'password123');

        expect($user)->toBeInstanceOf(User::class)
            ->and($user->email)->toBe('newuser@example.com')
            ->and($user->isClient($this->team))->toBeTrue()
            ->and($invitation->fresh()->isAccepted())->toBeTrue();
    });

    it('accepts invitation for existing user', function () {
        $existingUser = User::factory()->create([
            'is_admin' => false,
            'email' => 'existing@example.com',
        ]);

        $invitation = UserInvitation::factory()->create([
            'email' => $existingUser->email,
            'team_id' => $this->team->id,
            'role' => TeamRole::Coach,
            'invited_by' => $this->inviter->id,
            'expires_at' => now()->addDays(7),
        ]);

        $service = new UserInvitationService;
        $user = $service->acceptInvitation($invitation);

        expect($user->id)->toBe($existingUser->id)
            ->and($user->isCoach($this->team))->toBeTrue()
            ->and($invitation->fresh()->isAccepted())->toBeTrue();
    });

    it('throws exception for expired invitation', function () {
        $invitation = UserInvitation::factory()->create([
            'email' => 'newuser@example.com',
            'team_id' => $this->team->id,
            'role' => TeamRole::Client,
            'invited_by' => $this->inviter->id,
            'expires_at' => now()->subDay(),
        ]);

        $service = new UserInvitationService;

        expect(fn () => $service->acceptInvitation($invitation, 'password123'))
            ->toThrow(\Exception::class, 'This invitation has expired.');
    });

    it('throws exception for already accepted invitation', function () {
        $invitation = UserInvitation::factory()->create([
            'email' => 'newuser@example.com',
            'team_id' => $this->team->id,
            'role' => TeamRole::Client,
            'invited_by' => $this->inviter->id,
            'expires_at' => now()->addDays(7),
            'accepted_at' => now(),
        ]);

        $service = new UserInvitationService;

        expect(fn () => $service->acceptInvitation($invitation, 'password123'))
            ->toThrow(\Exception::class, 'This invitation has already been accepted.');
    });
});

describe('AcceptInvitation Livewire Component', function () {
    it('shows invitation details for valid token', function () {
        $invitation = UserInvitation::factory()->create([
            'email' => 'newuser@example.com',
            'team_id' => $this->team->id,
            'role' => TeamRole::Client,
            'invited_by' => $this->inviter->id,
            'expires_at' => now()->addDays(7),
        ]);

        Livewire::test(\App\Livewire\AcceptInvitation::class, ['token' => $invitation->token])
            ->assertSee('newuser@example.com')
            ->assertSee($this->team->name)
            ->assertSee('Client');
    });

    it('shows error for invalid token', function () {
        Livewire::test(\App\Livewire\AcceptInvitation::class, ['token' => 'invalid-token'])
            ->assertSee('Invalid invitation link');
    });

    it('shows expired message for expired invitation', function () {
        $invitation = UserInvitation::factory()->create([
            'email' => 'newuser@example.com',
            'team_id' => $this->team->id,
            'role' => TeamRole::Client,
            'invited_by' => $this->inviter->id,
            'expires_at' => now()->subDay(),
        ]);

        Livewire::test(\App\Livewire\AcceptInvitation::class, ['token' => $invitation->token])
            ->assertSee('Invitation Expired');
    });

    it('shows already accepted message', function () {
        $invitation = UserInvitation::factory()->create([
            'email' => 'newuser@example.com',
            'team_id' => $this->team->id,
            'role' => TeamRole::Client,
            'invited_by' => $this->inviter->id,
            'expires_at' => now()->addDays(7),
            'accepted_at' => now(),
        ]);

        Livewire::test(\App\Livewire\AcceptInvitation::class, ['token' => $invitation->token])
            ->assertSee('Invitation Accepted')
            ->assertSee('already been accepted');
    });

    it('accepts invitation for new user with password', function () {
        $invitation = UserInvitation::factory()->create([
            'email' => 'brandnew@example.com',
            'team_id' => $this->team->id,
            'role' => TeamRole::Client,
            'invited_by' => $this->inviter->id,
            'expires_at' => now()->addDays(7),
        ]);

        Livewire::test(\App\Livewire\AcceptInvitation::class, ['token' => $invitation->token])
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('accept')
            ->assertRedirect('/app/'.$this->team->slug);

        expect(User::where('email', 'brandnew@example.com')->exists())->toBeTrue()
            ->and($invitation->fresh()->isAccepted())->toBeTrue();
    });

    it('validates password for new user', function () {
        $invitation = UserInvitation::factory()->create([
            'email' => 'brandnew@example.com',
            'team_id' => $this->team->id,
            'role' => TeamRole::Client,
            'invited_by' => $this->inviter->id,
            'expires_at' => now()->addDays(7),
        ]);

        Livewire::test(\App\Livewire\AcceptInvitation::class, ['token' => $invitation->token])
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('accept')
            ->assertHasErrors(['password']);
    });

    it('shows join team button for existing user without password fields', function () {
        $existingUser = User::factory()->create([
            'is_admin' => false,
            'email' => 'existing@example.com',
        ]);

        $invitation = UserInvitation::factory()->create([
            'email' => $existingUser->email,
            'team_id' => $this->team->id,
            'role' => TeamRole::Coach,
            'invited_by' => $this->inviter->id,
            'expires_at' => now()->addDays(7),
        ]);

        Livewire::test(\App\Livewire\AcceptInvitation::class, ['token' => $invitation->token])
            ->assertSet('userExists', true)
            ->assertSee('You already have an account')
            ->assertSee('Join Team')
            ->assertDontSee('Create Account');
    });

    it('allows existing user to accept invitation without password', function () {
        $existingUser = User::factory()->create([
            'is_admin' => false,
            'email' => 'existing@example.com',
        ]);

        $invitation = UserInvitation::factory()->create([
            'email' => $existingUser->email,
            'team_id' => $this->team->id,
            'role' => TeamRole::Coach,
            'invited_by' => $this->inviter->id,
            'expires_at' => now()->addDays(7),
        ]);

        Livewire::test(\App\Livewire\AcceptInvitation::class, ['token' => $invitation->token])
            ->call('accept')
            ->assertRedirect('/app/'.$this->team->slug);

        expect($existingUser->fresh()->isCoach($this->team))->toBeTrue()
            ->and($invitation->fresh()->isAccepted())->toBeTrue();
    });
});

describe('Invitation Route', function () {
    it('renders accept invitation page', function () {
        $invitation = UserInvitation::factory()->create([
            'email' => 'newuser@example.com',
            'team_id' => $this->team->id,
            'role' => TeamRole::Client,
            'invited_by' => $this->inviter->id,
            'expires_at' => now()->addDays(7),
        ]);

        $this->get('/invitation/accept/'.$invitation->token)
            ->assertSuccessful()
            ->assertSee('Accept Invitation');
    });
});
