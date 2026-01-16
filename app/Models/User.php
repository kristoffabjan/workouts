<?php

namespace App\Models;

use App\Enums\TeamRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements FilamentUser, HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->teams;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams()->whereKey($tenant)->exists();
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function createdExercises(): HasMany
    {
        return $this->hasMany(Exercise::class, 'created_by');
    }

    public function createdTrainings(): HasMany
    {
        return $this->hasMany(Training::class, 'created_by');
    }

    public function assignedTrainings(): BelongsToMany
    {
        return $this->belongsToMany(Training::class)
            ->withPivot('completed_at', 'feedback')
            ->withTimestamps();
    }

    public function getRoleInTeam(Team|int $team): ?TeamRole
    {
        $teamId = $team instanceof Team ? $team->id : $team;
        $pivot = $this->teams()->where('team_id', $teamId)->first()?->pivot;

        return $pivot ? TeamRole::from($pivot->role) : null;
    }

    public function hasRole(Team|int $team, TeamRole $role): bool
    {
        return $this->getRoleInTeam($team) === $role;
    }

    public function isAdmin(Team|int $team): bool
    {
        return $this->hasRole($team, TeamRole::Admin);
    }

    public function isCoach(Team|int $team): bool
    {
        return $this->hasRole($team, TeamRole::Coach);
    }

    public function isClient(Team|int $team): bool
    {
        return $this->hasRole($team, TeamRole::Client);
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
