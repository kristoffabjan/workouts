<?php

namespace App\Filament\Admin\Resources\AccessRequests;

use App\Filament\Admin\Resources\AccessRequests\Pages\ListAccessRequests;
use App\Filament\Admin\Resources\AccessRequests\Tables\AccessRequestsTable;
use App\Models\AccessRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class AccessRequestResource extends Resource
{
    protected static ?string $model = AccessRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserPlus;

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 120;

    public static function getNavigationLabel(): string
    {
        return __('access_requests.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('access_requests.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('access_requests.plural_model_label');
    }

    public static function table(Table $table): Table
    {
        return AccessRequestsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccessRequests::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
