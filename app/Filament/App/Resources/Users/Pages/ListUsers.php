<?php

namespace App\Filament\App\Resources\Users\Pages;

use App\Enums\TeamRole;
use App\Filament\App\Resources\Users\UserResource;
use App\Models\User;
use App\Services\UserInvitationService;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

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
                        ->unique(User::class, 'email', ignoreRecord: true)
                        ->helperText('If user exists, they will be added to this team directly.'),
                    Select::make('role')
                        ->options(TeamRole::class)
                        ->default(TeamRole::Client)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $service = app(UserInvitationService::class);
                    $team = Filament::getTenant();

                    $result = $service->inviteToTeam(
                        email: $data['email'],
                        team: $team,
                        role: $data['role'],
                        inviter: Auth::user(),
                    );

                    if ($result instanceof User) {
                        Notification::make()
                            ->success()
                            ->title('User added to team')
                            ->body("**{$result->name}** has been added to this team.")
                            ->send();
                    } else {
                        Notification::make()
                            ->success()
                            ->title('Invitation sent')
                            ->body("An invitation has been sent to **{$data['email']}**.")
                            ->send();
                    }
                })
                ->modalHeading('Invite User to Team')
                ->modalSubmitActionLabel('Send Invitation'),
        ];
    }
}
