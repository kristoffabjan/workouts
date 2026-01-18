<?php

namespace App\Filament\App\Pages;

use App\Enums\TeamRole;
use App\Models\Team;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeamSettings extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Team Settings';

    protected static ?string $title = 'Team Settings';

    protected static ?int $navigationSort = 100;

    protected string $view = 'filament.app.pages.team-settings';

    public function getTeam(): Team
    {
        return Filament::getTenant();
    }

    public function isOwner(): bool
    {
        return $this->getTeam()->owner_id === auth()->id();
    }

    public function canLeaveTeam(): bool
    {
        $team = $this->getTeam();

        if ($team->is_personal) {
            return false;
        }

        return ! $this->isOwner();
    }

    public function canTransferOwnership(): bool
    {
        $team = $this->getTeam();

        if ($team->is_personal) {
            return false;
        }

        return $this->isOwner();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('leaveTeam')
                ->label('Leave Team')
                ->icon(Heroicon::ArrowRightOnRectangle)
                ->color('danger')
                ->visible(fn (): bool => $this->canLeaveTeam())
                ->requiresConfirmation()
                ->modalHeading('Leave Team')
                ->modalDescription('Are you sure you want to leave this team? You will lose access to all team resources.')
                ->modalSubmitActionLabel('Leave Team')
                ->action(function (): void {
                    $team = $this->getTeam();
                    $user = auth()->user();

                    $user->teams()->detach($team->id);

                    Notification::make()
                        ->success()
                        ->title('Left team')
                        ->body("You have left **{$team->name}**.")
                        ->send();

                    $this->redirectToPersonalTeamOrFirstTeam();
                }),

            Action::make('transferOwnership')
                ->label('Transfer Ownership')
                ->icon(Heroicon::UserCircle)
                ->color('warning')
                ->visible(fn (): bool => $this->canTransferOwnership())
                ->form([
                    Select::make('new_owner_id')
                        ->label('New Owner')
                        ->options(fn (): array => $this->getTransferableUsers())
                        ->required()
                        ->helperText('Select a coach to transfer ownership to. You will remain as a coach in this team.'),
                ])
                ->requiresConfirmation()
                ->modalHeading('Transfer Ownership')
                ->modalDescription('Are you sure you want to transfer ownership? The new owner will have full control over this team.')
                ->modalSubmitActionLabel('Transfer Ownership')
                ->action(function (array $data): void {
                    $team = $this->getTeam();
                    $newOwnerId = $data['new_owner_id'];

                    DB::transaction(function () use ($team, $newOwnerId): void {
                        $team->update(['owner_id' => $newOwnerId]);

                        $team->users()->updateExistingPivot($newOwnerId, ['role' => TeamRole::Coach->value]);
                    });

                    Notification::make()
                        ->success()
                        ->title('Ownership transferred')
                        ->body('Team ownership has been transferred successfully.')
                        ->send();
                }),
        ];
    }

    private function getTransferableUsers(): array
    {
        $team = $this->getTeam();

        return $team->coaches()
            ->where('users.id', '!=', auth()->id())
            ->pluck('name', 'users.id')
            ->toArray();
    }

    private function redirectToPersonalTeamOrFirstTeam(): void
    {
        $user = Auth::user();
        $personalTeam = $user->personalTeam();

        if ($personalTeam) {
            $this->redirect(Filament::getUrl($personalTeam));

            return;
        }

        $firstTeam = $user->teams()->first();

        if ($firstTeam) {
            $this->redirect(Filament::getUrl($firstTeam));

            return;
        }

        $this->redirect(Filament::getUrl());
    }
}
