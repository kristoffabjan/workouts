<?php

namespace App\Models;

use App\Enums\AccessRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessRequest extends Model
{
    /** @use HasFactory<\Database\Factories\AccessRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'message',
        'status',
        'processed_at',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => AccessRequestStatus::class,
            'processed_at' => 'datetime',
        ];
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function isPending(): bool
    {
        return $this->status === AccessRequestStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === AccessRequestStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->status === AccessRequestStatus::Rejected;
    }

    public function approve(User $approver): void
    {
        $this->update([
            'status' => AccessRequestStatus::Approved,
            'processed_at' => now(),
            'processed_by' => $approver->id,
        ]);
    }

    public function reject(User $rejecter): void
    {
        $this->update([
            'status' => AccessRequestStatus::Rejected,
            'processed_at' => now(),
            'processed_by' => $rejecter->id,
        ]);
    }
}
