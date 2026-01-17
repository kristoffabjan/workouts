<?php

namespace App\Filament\App\Resources\Exercises;

use App\Filament\App\Resources\Exercises\Pages\CreateExercise;
use App\Filament\App\Resources\Exercises\Pages\EditExercise;
use App\Filament\App\Resources\Exercises\Pages\ListExercises;
use App\Filament\App\Resources\Exercises\Schemas\ExerciseForm;
use App\Filament\App\Resources\Exercises\Tables\ExercisesTable;
use App\Models\Exercise;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExerciseResource extends Resource
{
    protected static ?string $model = Exercise::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ExerciseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExercisesTable::configure($table);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'tags'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $tags = $record->tags ?? [];

        return [
            'Tags' => implode(', ', array_slice($tags, 0, 5)),
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExercises::route('/'),
            'create' => CreateExercise::route('/create'),
            'edit' => EditExercise::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
