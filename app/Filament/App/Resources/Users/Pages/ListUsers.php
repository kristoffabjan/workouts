<?php

namespace App\Filament\App\Resources\Users\Pages;

use App\Enums\TeamRole;
use App\Filament\App\Resources\Users\UserResource;
use App\Services\UserInvitationService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
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
                ->hidden(fn (): bool => Filament::getTenant() && Filament::getTenant()->is_personal)
                ->schema([
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->helperText('An invitation email will be sent. Existing users can join with one click.'),
                    Select::make('role')
                        ->options(TeamRole::class)
                        ->default(TeamRole::Client)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $service = app(UserInvitationService::class);
                    $team = Filament::getTenant();

                    $service->inviteToTeam(
                        email: $data['email'],
                        team: $team,
                        role: $data['role'],
                        inviter: auth()->user(),
                    );

                    Notification::make()
                        ->success()
                        ->title('Invitation sent')
                        ->body("An invitation has been sent to **{$data['email']}**.")
                        ->send();
                })
                ->modalHeading('Invite User to Team')
                ->modalSubmitActionLabel('Send Invitation'),
        ];
    }
}
