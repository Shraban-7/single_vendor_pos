<?php

namespace App\Models;

use App\Enums\NotificationStatus;
use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'type',
        'recipient',
        'template',
        'message',
        'status',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'type' => NotificationType::class,
        'status' => NotificationStatus::class,
        'sent_at' => 'datetime',
    ];

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', NotificationStatus::PENDING);
    }

    public function scopeSent($query)
    {
        return $query->where('status', NotificationStatus::SENT);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', NotificationStatus::FAILED);
    }

    public function scopeSms($query)
    {
        return $query->where('type', NotificationType::SMS);
    }

    public function scopeEmail($query)
    {
        return $query->where('type', NotificationType::EMAIL);
    }

    // Helpers
    public function markAsSent(): void
    {
        $this->update([
            'status' => NotificationStatus::SENT,
            'sent_at' => now(),
        ]);
    }

    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => NotificationStatus::FAILED,
            'error_message' => $errorMessage,
        ]);
    }

    public function isSent(): bool
    {
        return $this->status === NotificationStatus::SENT;
    }

    public function isFailed(): bool
    {
        return $this->status === NotificationStatus::FAILED;
    }
}
