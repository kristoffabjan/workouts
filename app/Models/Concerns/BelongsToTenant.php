<?php

namespace App\Models\Concerns;

use App\Models\Team;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $query) {
            $tenant = Filament::getTenant();

            if ($tenant instanceof Team) {
                $query->where($query->getModel()->getTable().'.team_id', $tenant->id);
            }
        });

        static::creating(function (Model $model) {
            $tenant = Filament::getTenant();

            if ($tenant instanceof Team && ! $model->team_id) {
                $model->team_id = $tenant->id;
            }
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
