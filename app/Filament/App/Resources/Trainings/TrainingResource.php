<?php

namespace App\Filament\App\Resources\Trainings;

use App\Enums\TrainingStatus;
use App\Filament\App\Resources\Trainings\Pages\CreateTraining;
use App\Filament\App\Resources\Trainings\Pages\EditTraining;
use App\Filament\App\Resources\Trainings\Pages\ListTrainings;
use App\Filament\App\Resources\Trainings\Pages\ViewTraining;
use App\Filament\App\Resources\Trainings\RelationManagers\AssignedUsersRelationManager;
use App\Filament\App\Resources\Trainings\RelationManagers\ExercisesRelationManager;
use App\Filament\App\Resources\Trainings\Schemas\TrainingForm;
use App\Filament\App\Resources\Trainings\Tables\TrainingsTable;
use App\Models\Training;
use BackedEnum;
use Dom\Text;
use Filament\Facades\Filament;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrainingResource extends Resource
{
    protected static ?string $model = Training::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return TrainingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Training Details')
                ->schema([
                    TextEntry::make('title'),
                    TextEntry::make('status')
                        ->formatStateUsing(fn (TrainingStatus $state): string => $state->getLabel())
                        ->icon(fn (TrainingStatus $state): mixed => $state->getIcon())
                        ->color(fn (TrainingStatus $state): array => $state->getColor()),
                    TextEntry::make('scheduled_at')
                        ->label('Scheduled At')
                        ->dateTime(),
                    TextEntry::make('creator.name')
                        ->label('Created By'),
                ])
                ->columns(2),
            Section::make('Content')
                ->schema([
                    TextEntry::make('content')
                        ->html()
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(fn ($record) => empty($record->content)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return TrainingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ExercisesRelationManager::class,
            AssignedUsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainings::route('/'),
            'create' => CreateTraining::route('/create'),
            'view' => ViewTraining::route('/{record}'),
            'edit' => EditTraining::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        $team = Filament::getTenant();

        if (! $user || ! $team) {
            return $query;
        }

        if ($user->isClient($team)) {
            $query->whereHas('assignedUsers', function (Builder $q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query;
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
