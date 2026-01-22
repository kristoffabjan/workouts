<?php

namespace App\Filament\Admin\Resources\Trainings;

use App\Filament\Admin\Resources\Trainings\Pages\EditTraining;
use App\Filament\Admin\Resources\Trainings\Pages\ListTrainings;
use App\Filament\Admin\Resources\Trainings\Pages\ViewTraining;
use App\Filament\Admin\Resources\Trainings\RelationManagers\AssignedUsersRelationManager;
use App\Filament\Admin\Resources\Trainings\RelationManagers\ExercisesRelationManager;
use App\Filament\Admin\Resources\Trainings\Schemas\TrainingForm;
use App\Filament\Admin\Resources\Trainings\Tables\TrainingsTable;
use App\Models\Training;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class TrainingResource extends Resource
{
    protected static ?string $model = Training::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 60;

    protected static ?string $navigationLabel = 'All Trainings';

    public static function form(Schema $schema): Schema
    {
        return TrainingForm::configure($schema);
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes()
            ->with(['team', 'creator', 'assignedUsers', 'exercises']);
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainings::route('/'),
            'view' => ViewTraining::route('/{record}'),
            'edit' => EditTraining::route('/{record}/edit'),
        ];
    }
}
