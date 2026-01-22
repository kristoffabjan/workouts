<?php

namespace App\Notifications;

use App\Models\Training;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TrainingCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Training $training,
        public User $client,
        public ?string $feedback = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'training_id' => $this->training->id,
            'training_title' => $this->training->title,
            'client_id' => $this->client->id,
            'client_name' => $this->client->name,
            'feedback' => $this->feedback,
            'message' => "{$this->client->name} completed training: {$this->training->title}",
        ];
    }
}
