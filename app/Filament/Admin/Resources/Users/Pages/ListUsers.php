<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use App\Models\User;
use App\Services\UserInvitationService;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('inviteUser')
                ->label('Invite User')
                ->icon('heroicon-o-user-plus')
                ->form([
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(User::class, 'email')
                        ->helperText('User will receive an invitation email and a personal team will be created for them.'),
                ])
                ->action(function (array $data): void {
                    $service = app(UserInvitationService::class);

                    $service->inviteAsIndividual(
                        email: $data['email'],
                        inviter: auth()->user(),
                    );

                    Notification::make()
                        ->success()
                        ->title('Invitation sent')
                        ->body("An invitation has been sent to **{$data['email']}**.")
                        ->send();
                })
                ->modalHeading('Invite User')
                ->modalDescription('The user will receive an invitation email. Upon acceptance, a personal team will be created for them automatically.')
                ->modalSubmitActionLabel('Send Invitation'),
        ];
    }
}
