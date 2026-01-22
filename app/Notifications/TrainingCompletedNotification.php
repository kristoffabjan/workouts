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
            ? "Feedback: {$this->feedback}"
            : 'No feedback provided';

        return FilamentNotification::make()
            ->title("{$this->client->name} completed training")
            ->body($body)
            ->icon('heroicon-o-check-circle')
            ->success()
            ->actions([
                Action::make('view')
                    ->label('View Training')
                    ->url(TrainingResource::getUrl('view', [
                        'record' => $this->training,
                        'tenant' => $this->training->team,
                    ])),
            ])
            ->getDatabaseMessage();
    }
}
