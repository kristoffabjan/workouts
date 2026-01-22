<?php

namespace App\Filament\App\Resources\Trainings\Tables;

use App\Enums\TeamRole;
use App\Enums\TrainingStatus;
use App\Filament\App\Resources\Trainings\Actions\ScheduleTrainingAction;
use App\Filament\App\Resources\Trainings\TrainingResource;
use App\Models\Training;
use App\Models\User;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TrainingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(TrainingStatus::class),
                Filter::make('scheduled_at')
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('scheduled_from')
                            ->label('From'),
                        \Filament\Forms\Components\DatePicker::make('scheduled_until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['scheduled_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('scheduled_at', '>=', $date),
                            )
                            ->when(
                                $data['scheduled_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('scheduled_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['scheduled_from'] ?? null) {
                            $indicators['scheduled_from'] = 'From ' . \Carbon\Carbon::parse($data['scheduled_from'])->toFormattedDateString();
                        }

                        if ($data['scheduled_until'] ?? null) {
                            $indicators['scheduled_until'] = 'Until ' . \Carbon\Carbon::parse($data['scheduled_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
                SelectFilter::make('assigned_user')
                    ->label('Assigned To')
                    ->options(function () {
                        $team = Filament::getTenant();
                        if (! $team) {
                            return [];
                        }

                        return User::whereHas('teams', function (Builder $query) use ($team) {
                            $query->where('team_id', $team->id)
                                ->where('role', TeamRole::Client->value);
                        })->pluck('name', 'id')->toArray();
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereHas('assignedUsers', function (Builder $q) use ($data) {
                            $q->where('user_id', $data['value']);
                        });
                    })
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ScheduleTrainingAction::makeTableAction(),
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->recordUrl(function (Training $record): string {
                $team = Filament::getTenant();
                $user = auth()->user();

                if ($team && $user && $user->isCoach($team)) {
                    return TrainingResource::getUrl('edit', ['record' => $record]);
                }

                return TrainingResource::getUrl('view', ['record' => $record]);
            })
            ->defaultSort('scheduled_at', 'desc');
    }
}
