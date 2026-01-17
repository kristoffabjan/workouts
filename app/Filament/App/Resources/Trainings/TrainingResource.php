<?php

namespace App\Filament\App\Resources\Trainings;

use App\Filament\App\Resources\Trainings\Pages\CreateTraining;
use App\Filament\App\Resources\Trainings\Pages\EditTraining;
use App\Filament\App\Resources\Trainings\Pages\ListTrainings;
use App\Filament\App\Resources\Trainings\Schemas\TrainingForm;
use App\Filament\App\Resources\Trainings\Tables\TrainingsTable;
use App\Models\Training;
use BackedEnum;
use Filament\Resources\Resource;
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

    public static function table(Table $table): Table
    {
        return TrainingsTable::configure($table);
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
            'index' => ListTrainings::route('/'),
            'create' => CreateTraining::route('/create'),
            'edit' => EditTraining::route('/{record}/edit'),
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
