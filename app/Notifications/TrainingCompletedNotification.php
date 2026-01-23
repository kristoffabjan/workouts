<?php

namespace App\Notifications;

use App\Filament\App\Resources\Trainings\TrainingResource;
use App\Models\Training;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
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

    public function toDatabase(object $notifiable): array
    {
        $body = $this->feedback
            ? __('notifications.training_completed.feedback', ['feedback' => $this->feedback])
            : __('notifications.training_completed.no_feedback');

        return FilamentNotification::make()
            ->title(__('notifications.training_completed.title', ['name' => $this->client->name]))
            ->body($body)
            ->icon('heroicon-o-check-circle')
            ->success()
            ->actions([
                Action::make('view')
                    ->label(__('notifications.training_completed.view_training'))
                    ->url(TrainingResource::getUrl('view', [
                        'record' => $this->training,
                        'tenant' => $this->training->team,
                    ])),
            ])
            ->getDatabaseMessage();
    }
}
