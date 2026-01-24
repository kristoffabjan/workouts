<?php

namespace App\Filament\Admin\Resources\AccessRequests\Tables;

use App\Enums\AccessRequestStatus;
use App\Models\AccessRequest;
use App\Models\User;
use App\Services\UserInvitationService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AccessRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('access_requests.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('access_requests.fields.email'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('message')
                    ->label(__('access_requests.fields.message'))
                    ->limit(50)
                    ->tooltip(fn (AccessRequest $record) => $record->message)
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('access_requests.fields.status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('processedBy.name')
                    ->label(__('access_requests.fields.processed_by'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('processed_at')
                    ->label(__('access_requests.fields.processed_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('access_requests.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(AccessRequestStatus::class)
                    ->label(__('access_requests.fields.status')),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label(__('access_requests.actions.approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(__('access_requests.actions.approve_heading'))
                    ->modalDescription(__('access_requests.actions.approve_description'))
                    ->visible(fn (AccessRequest $record) => $record->isPending())
                    ->action(function (AccessRequest $record) {
                        $record->approve(auth()->user());

                        if (! User::where('email', $record->email)->exists()) {
                            $invitationService = app(UserInvitationService::class);
                            $invitationService->inviteAsIndividual($record->email, auth()->user());
                        }

                        Notification::make()
                            ->success()
                            ->title(__('access_requests.messages.approved'))
                            ->body(__('access_requests.messages.invitation_sent'))
                            ->send();
                    }),
                Action::make('reject')
                    ->label(__('access_requests.actions.reject'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(__('access_requests.actions.reject_heading'))
                    ->modalDescription(__('access_requests.actions.reject_description'))
                    ->visible(fn (AccessRequest $record) => $record->isPending())
                    ->action(function (AccessRequest $record) {
                        $record->reject(auth()->user());

                        Notification::make()
                            ->success()
                            ->title(__('access_requests.messages.rejected'))
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
