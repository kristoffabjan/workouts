<?php

namespace App\Models;

use App\Enums\TeamRole;
use App\Enums\WeightUnit;
use App\Observers\UserObserver;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[ObservedBy([UserObserver::class])]
class User extends Authenticatable implements FilamentUser, HasAvatar, HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'settings',
        'email_verified_at',
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
            'settings' => 'array',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => (bool) $this->is_admin,
            'app' => true,
            default => false,
        };
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
        return $this->belongsToMany(Training::class, 'training_user')
            ->using(TrainingUser::class)
            ->withPivot('id', 'completed_at', 'feedback')
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

    public function isCoach(Team|int $team): bool
    {
        return $this->hasRole($team, TeamRole::Coach);
    }

    public function isClient(Team|int $team): bool
    {
        return $this->hasRole($team, TeamRole::Client);
    }

    public function personalTeam(): ?Team
    {
        return $this->teams()->where('is_personal', true)->first();
    }

    public function hasPersonalTeam(): bool
    {
        return $this->personalTeam() !== null;
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function getPreferredLanguage(): ?string
    {
        return $this->settings['preferred_language'] ?? null;
    }

    public function getWeightUnit(): WeightUnit
    {
        $value = $this->settings['weight_unit'] ?? WeightUnit::Kg->value;

        return WeightUnit::from($value);
    }

    public function getAvatar(): ?string
    {
        return $this->settings['avatar'] ?? null;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $avatar = $this->getAvatar();

        if (! $avatar) {
            return null;
        }

        return Storage::disk('public')->url($avatar);
    }

    public function updateSettings(array $settings): void
    {
        $this->update([
            'settings' => array_merge($this->settings ?? [], $settings),
        ]);
    }
}
