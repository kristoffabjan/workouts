<?php

namespace App\Filament\App\Resources\Trainings\RelationManagers;

use App\Enums\TeamRole;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AssignedUsersRelationManager extends RelationManager
{
    protected static string $relationship = 'assignedUsers';

    protected static ?string $inverseRelationship = 'assignedTrainings';

    protected static ?string $title = 'Assigned Clients';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('email'),
                TextEntry::make('pivot.created_at')
                    ->label('Assigned At')
                    ->dateTime(),
                TextEntry::make('pivot.completed_at')
                    ->label('Completed At')
                    ->dateTime()
                    ->placeholder('Not completed'),
                TextEntry::make('pivot.feedback')
                    ->label('Feedback')
                    ->placeholder('No feedback provided')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('pivot.completed_at')
                    ->label('Completed')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),
                TextColumn::make('pivot.completed_at')
                    ->label('Completed At')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('pivot.feedback')
                    ->label('Feedback')
                    ->limit(30)
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('completed')
                    ->label('Completion Status')
                    ->placeholder('All')
                    ->trueLabel('Completed')
                    ->falseLabel('Pending')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('training_user.completed_at'),
                        false: fn (Builder $query) => $query->whereNull('training_user.completed_at'),
                    ),
            ])
            ->headerActions([
                AttachAction::make()
                    ->preloadRecordSelect()
                    ->multiple()
                    ->visible(fn (): bool => ! $this->isScheduledInPast())
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        $team = Filament::getTenant();
                        if ($team) {
                            $query->whereHas('teams', function (Builder $q) use ($team) {
                                $q->where('team_id', $team->id)
                                    ->where('role', TeamRole::Client->value);
                            });
                        }

                        return $query;
                    })
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Client')
                            ->searchable()
                            ->preload(),
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                DetachAction::make()
                    ->visible(fn (): bool => ! $this->isScheduledInPast()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->orderBy('training_user.created_at', 'desc'));
    }

    protected function isScheduledInPast(): bool
    {
        $ownerRecord = $this->getOwnerRecord();

        return $ownerRecord->scheduled_at && $ownerRecord->scheduled_at->isPast();
    }
}
