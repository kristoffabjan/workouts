<?php

namespace App\Filament\App\Resources\Users\Pages;

use App\Enums\TeamRole;
use App\Filament\App\Resources\Users\UserResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $tenant = Filament::getTenant();
        $role = $this->record->getRoleInTeam($tenant);
        $data['team_role'] = $role?->value;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['team_role']);

        return $data;
    }

    protected function afterSave(): void
    {
        $tenant = Filament::getTenant();
        $role = $this->data['team_role'] ?? null;

        if ($role && $tenant) {
            $this->record->teams()->updateExistingPivot($tenant->id, [
                'role' => TeamRole::from($role)->value,
            ]);
        }
    }
}
