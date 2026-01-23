<?php

namespace App\Filament\App\Resources\Users;

use App\Filament\App\Resources\Users\Pages\EditUser;
use App\Filament\App\Resources\Users\Pages\ListUsers;
use App\Filament\App\Resources\Users\Pages\ViewUser;
use App\Filament\App\Resources\Users\RelationManagers\AssignedTrainingsRelationManager;
use App\Filament\App\Resources\Users\Schemas\UserForm;
use App\Filament\App\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?int $navigationSort = 30;

    protected static ?string $tenantOwnershipRelationshipName = 'teams';

    public static function getModelLabel(): string
    {
        return __('users.resource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('users.resource.plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return __('users.resource.navigation_label');
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $tenant = Filament::getTenant();

        return parent::getEloquentQuery()
            ->whereHas('teams', fn (Builder $query) => $query->where('team_id', $tenant?->id));
    }

    public static function getRelations(): array
    {
        return [
            AssignedTrainingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
