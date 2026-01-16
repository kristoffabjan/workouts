<?php

namespace App\Models;

use App\Enums\TrainingStatus;
use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Training extends Model
{
    /** @use HasFactory<\Database\Factories\TrainingFactory> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id',
        'title',
        'content',
        'status',
        'scheduled_date',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => TrainingStatus::class,
            'scheduled_date' => 'date',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function exercises(): BelongsToMany
    {
        return $this->belongsToMany(Exercise::class)
            ->withPivot('notes', 'sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('completed_at', 'feedback')
            ->withTimestamps();
    }

    public function scopeForTeam(Builder $query, int $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', TrainingStatus::Scheduled);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', TrainingStatus::Draft);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', TrainingStatus::Completed);
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
