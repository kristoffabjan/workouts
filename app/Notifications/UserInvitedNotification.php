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
            ->subject(__('notifications.user_invited.team_subject'))
            ->greeting('Hello!')
            ->line(__('notifications.user_invited.team_greeting', ['team_name' => $this->team->name]));

        $acceptUrl = url('/invitation/accept/'.$this->token);

        return $message
            ->line(__('notifications.user_invited.team_line'))
            ->action(__('notifications.user_invited.team_button'), $acceptUrl)
            ->line(__('notifications.user_invited.team_expire'));
    }

    private function newUserMail(): MailMessage
    {
        $message = (new MailMessage)
            ->subject(__('notifications.user_invited.individual_subject'))
            ->greeting('Hello!');

        if ($this->team) {
            $message->line(__('notifications.user_invited.team_greeting', ['team_name' => $this->team->name]));
        } else {
            $message->line(__('notifications.user_invited.individual_greeting'));
        }

        $acceptUrl = url('/invitation/accept/'.$this->token);

        return $message
            ->line(__('notifications.user_invited.team_line'))
            ->action(__('notifications.user_invited.individual_button'), $acceptUrl)
            ->line(__('notifications.user_invited.team_expire'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'team_id' => $this->team?->id,
            'token' => $this->token,
        ];
    }
}
