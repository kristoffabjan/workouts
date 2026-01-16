<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    /** @use HasFactory<\Database\Factories\ExerciseFactory> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id',
        'name',
        'description',
        'video_urls',
        'tags',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'video_urls' => 'array',
            'tags' => 'array',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function trainings(): BelongsToMany
    {
        return $this->belongsToMany(Training::class)
            ->withPivot('notes', 'sort_order')
            ->withTimestamps();
    }

    public function scopeForTeam(Builder $query, int $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }

    public function scopePersonal(Builder $query): Builder
    {
        return $query->whereNull('team_id');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('created_by', $userId);
    }

    public function isPersonal(): bool
    {
        return $this->team_id === null;
    }
}
