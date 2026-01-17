<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public ?Team $team = null,
        public bool $isExistingUser = false,
        public ?string $token = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->isExistingUser) {
            return $this->existingUserMail();
        }

        return $this->newUserMail();
    }

    private function existingUserMail(): MailMessage
    {
        $message = (new MailMessage)
            ->subject('You have been added to a team')
            ->greeting('Hello!')
            ->line("You have been added to the team: **{$this->team->name}**.");

        $message->action('Go to App', url('/app/'.$this->team->slug));

        return $message->line('You can now access this team using your existing account.');
    }

    private function newUserMail(): MailMessage
    {
        $message = (new MailMessage)
            ->subject('You have been invited to Workouts App')
            ->greeting('Hello!');

        if ($this->team) {
            $message->line("You have been invited to join the team: **{$this->team->name}**.");
        } else {
            $message->line('You have been invited to join the Workouts App.');
        }

        $acceptUrl = url('/invitation/accept/'.$this->token);

        return $message
            ->line('Click the button below to create your account and get started.')
            ->action('Accept Invitation', $acceptUrl)
            ->line('This invitation will expire in 7 days.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'team_id' => $this->team?->id,
            'token' => $this->token,
        ];
    }
}
