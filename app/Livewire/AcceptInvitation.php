<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\UserInvitation;
use App\Services\UserInvitationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class AcceptInvitation extends Component
{
    public string $token = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $isExpired = false;

    public bool $isAccepted = false;

    public bool $userExists = false;

    public ?string $error = null;

    public ?string $email = null;

    public ?string $teamName = null;

    public ?string $teamSlug = null;

    public ?string $roleName = null;

    public function mount(string $token): void
    {
        $this->token = $token;
        $invitation = $this->getInvitation();

        if (! $invitation) {
            $this->error = 'Invalid invitation link.';

            return;
        }

        $this->email = $invitation->email;
        $this->teamName = $invitation->team?->name;
        $this->teamSlug = $invitation->team?->slug;
        $this->roleName = $invitation->role?->getLabel();

        if ($invitation->isExpired()) {
            $this->isExpired = true;

            return;
        }

        if ($invitation->isAccepted()) {
            $this->isAccepted = true;

            return;
        }

        $this->userExists = User::where('email', $invitation->email)->exists();
    }

    public function accept(): void
    {
        if ($this->error || $this->isExpired || $this->isAccepted) {
            Log::info('Attempt to accept invalid invitation.', [
                'token' => $this->token,
            ]);
            return;
        }

        if (! $this->userExists) {
            $this->validate([
                'password' => ['required', 'min:8', 'confirmed'],
            ]);
        }

        try {
            $invitation = $this->getInvitation();

            if (! $invitation) {
                $this->error = 'Invalid invitation.';

                Log::info('Invitation not found during acceptance.', [
                    'token' => $this->token,
                ]);
                return;
            }

            $service = new UserInvitationService;
            $user = $service->acceptInvitation(
                invitation: $invitation,
                password: $this->userExists ? null : $this->password,
            );

            Auth::login($user);

            if ($this->teamSlug) {
                $this->redirect('/app/'.$this->teamSlug);
            } else {
                $this->redirect('/app');
            }
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            Log::error('Error accepting invitation: '.$e->getMessage(), [
                'token' => $this->token,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.accept-invitation')
            ->layout('layouts.auth', ['title' => 'Accept Invitation']);
    }

    private function getInvitation(): ?UserInvitation
    {
        return UserInvitation::where('token', $this->token)->first();
    }
}
