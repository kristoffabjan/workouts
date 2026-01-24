<?php

namespace App\Notifications;

use App\Filament\Admin\Resources\AccessRequests\AccessRequestResource;
use App\Models\AccessRequest;
use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccessRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public AccessRequest $accessRequest,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        return (new MailMessage)
            ->subject(__('notifications.access_request.subject'))
            ->greeting(__('notifications.access_request.greeting'))
            ->line(__('notifications.access_request.line', [
                'name' => $this->accessRequest->name,
                'email' => $this->accessRequest->email,
            ]))
            ->when($this->accessRequest->message, fn ($mail) => $mail->line(__('notifications.access_request.message_label').': '.$this->accessRequest->message))
            ->action(__('notifications.access_request.action'), AccessRequestResource::getUrl('index'))
            ->line(__('notifications.access_request.footer'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'access_request_id' => $this->accessRequest->id,
            'name' => $this->accessRequest->name,
            'email' => $this->accessRequest->email,
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        return [
            'title' => __('notifications.access_request.database_title'),
            'body' => __('notifications.access_request.database_body', [
                'name' => $this->accessRequest->name,
                'email' => $this->accessRequest->email,
            ]),
            'actions' => [
                [
                    'name' => 'view',
                    'label' => __('notifications.access_request.action'),
                    'url' => AccessRequestResource::getUrl('index'),
                ],
            ],
        ];
    }
}
