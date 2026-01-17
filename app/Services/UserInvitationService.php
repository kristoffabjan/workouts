<?php

namespace App\Services;

use App\Enums\TeamRole;
use App\Models\Team;
use App\Models\User;
use App\Models\UserInvitation;
use App\Notifications\UserInvitedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class UserInvitationService
{
    public function inviteToTeam(string $email, Team $team, TeamRole $role, User $inviter): UserInvitation|User
    {
        $existingUser = User::where('email', $email)->first();

        if ($existingUser) {
            return $this->attachExistingUserToTeam($existingUser, $team, $role);
        }

        return $this->createInvitation($email, $team, $role, $inviter);
    }

    public function inviteAsIndividual(string $email, User $inviter): UserInvitation
    {
        return $this->createInvitation($email, null, null, $inviter);
    }

    public function acceptInvitation(UserInvitation $invitation, ?string $password = null): User
    {
        if ($invitation->isExpired()) {
            throw new \Exception('This invitation has expired.');
        }

        if ($invitation->isAccepted()) {
            throw new \Exception('This invitation has already been accepted.');
        }

        return DB::transaction(function () use ($invitation, $password) {
            $user = User::where('email', $invitation->email)->first();

            if (! $user) {
                $user = User::create([
                    'name' => $this->extractNameFromEmail($invitation->email),
                    'email' => $invitation->email,
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                ]);
            }

            if ($invitation->isForTeam()) {
                $user->teams()->syncWithoutDetaching([
                    $invitation->team_id => ['role' => $invitation->role->value],
                ]);
            }

            $invitation->update(['accepted_at' => now()]);

            return $user;
        });
    }

    private function attachExistingUserToTeam(User $user, Team $team, TeamRole $role): User
    {
        if (! $user->teams()->where('team_id', $team->id)->exists()) {
            $user->teams()->attach($team, ['role' => $role->value]);
        }

        $user->notify(new UserInvitedNotification(team: $team, isExistingUser: true));

        return $user;
    }

    private function createInvitation(string $email, ?Team $team, ?TeamRole $role, User $inviter): UserInvitation
    {
        $invitation = UserInvitation::create([
            'email' => $email,
            'team_id' => $team?->id,
            'role' => $role,
            'token' => UserInvitation::generateToken(),
            'invited_by' => $inviter->id,
            'expires_at' => now()->addDays(7),
        ]);

        $this->sendInvitationEmail($invitation);

        return $invitation;
    }

    private function sendInvitationEmail(UserInvitation $invitation): void
    {
        Notification::route('mail', $invitation->email)
            ->notify(new UserInvitedNotification(
                team: $invitation->team,
                isExistingUser: false,
                token: $invitation->token,
            ));
    }

    private function extractNameFromEmail(string $email): string
    {
        $localPart = explode('@', $email)[0];

        return str($localPart)
            ->replace(['.', '_', '-', '+'], ' ')
            ->title()
            ->toString();
    }
}
