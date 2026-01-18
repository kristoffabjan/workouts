<?php

namespace App\Models;

use App\Enums\TeamRole;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'is_personal',
        'owner_id',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'is_personal' => 'boolean',
            'settings' => 'array',
        ];
    }

    /* public function getFilamentAvatarUrl(): ?string // needs to implement HasAvatar
    {
        return $this->avatar_url;
    } */

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function isPersonal(): bool
    {
        return $this->is_personal;
    }

    public function scopeOrganizations(Builder $query): Builder
    {
        return $query->where('is_personal', false);
    }

    public function scopePersonal(Builder $query): Builder
    {
        return $query->where('is_personal', true);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(Training::class);
    }

    public function coaches(): BelongsToMany
    {
        return $this->users()->wherePivot('role', TeamRole::Coach->value);
    }

    public function clients(): BelongsToMany
    {
        return $this->users()->wherePivot('role', TeamRole::Client->value);
    }
}
