<?php

namespace App\Models;

use App\Enums\DeliveryZone;
use App\Enums\PaymentMethod;
use App\Enums\PaymentMethodType;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\SaleStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sales';

    protected static function booted()
    {
        static::created(function ($sale) {
            $admins = \App\Models\User::whereIn('role', \App\Enums\UserRole::staffRoles())->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'New Sale Received',
                    'message' => "Sale {$sale->invoice_number} has been placed.",
                    'type' => \App\Enums\SystemNotificationType::SALE_CREATED,
                    'action_url' => route('admin.sales.show', $sale->invoice_number),
                ]);
            }
        });

        static::updated(function ($sale) {
            if ($sale->wasChanged('status')) {
                $status = $sale->status;
                $notificationType = match ($status) {
                    \App\Enums\SaleStatus::PENDING => \App\Enums\SystemNotificationType::SALE_CREATED,
                    \App\Enums\SaleStatus::CONFIRMED => \App\Enums\SystemNotificationType::SALE_CONFIRMED,
                    \App\Enums\SaleStatus::SHIPPED => \App\Enums\SystemNotificationType::SALE_SHIPPED,
                    \App\Enums\SaleStatus::DELIVERED => \App\Enums\SystemNotificationType::SALE_DELIVERED,
                    \App\Enums\SaleStatus::CANCELLED => \App\Enums\SystemNotificationType::SALE_CANCELLED,
                    default => null,
                };

                if (!$notificationType) {
                    return;
                }

                $admins = \App\Models\User::whereIn('role', \App\Enums\UserRole::staffRoles())->get();

                foreach ($admins as $admin) {
                    \App\Models\Notification::create([
                        'user_id' => $admin->id,
                        'title' => 'Sale Status Updated',
                        'message' => "Sale {$sale->invoice_number} status changed to {$status->value}.",
                        'type' => $notificationType,
                        'action_url' => route('admin.sales.show', $sale->order_number),
                    ]);
                }
            }
        });
    }

    protected $guarded = ['id'];

    protected $casts = [
        'sale_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'returned_amount' => 'decimal:2',
        'status' => \App\Enums\SaleStatus::class,
        'payment_status' => \App\Enums\PaymentStatus::class,
        'discount_type' => \App\Enums\DiscountType::class,
        'return_status' => \App\Enums\ReturnStatus::class,
        'has_return' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'reference');
    }

    public function scopeByStatus($query, SaleStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', SaleStatus::PENDING);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', SaleStatus::activeStatuses());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', SaleStatus::DELIVERED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', SaleStatus::CANCELLED);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', PaymentStatus::PAID);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', PaymentStatus::PENDING);
    }

    public function updateStatus(SaleStatus $status, ?string $comment = null, ?string $updatedBy = null): void
    {
        $this->update(['status' => $status]);

        match ($status) {
            SaleStatus::CONFIRMED => $this->update(['confirmed_at' => now()]),
            SaleStatus::SHIPPED => $this->update(['shipped_at' => now()]),
            SaleStatus::DELIVERED => $this->update(['delivered_at' => now()]),
            SaleStatus::CANCELLED => $this->update(['cancelled_at' => now()]),
            default => null,
        };

        $this->statusHistories()->create([
            'status' => $status->value,
            'comment' => $comment,
            'updated_by' => $updatedBy ?? 'system',
        ]);
    }

    public function markAsPaid(?string $transactionId = null): void
    {
        $this->update([
            'payment_status' => PaymentStatus::PAID,
            'transaction_id' => $transactionId ?? $this->transaction_id,
            'paid_at' => now(),
        ]);
    }

    public function cancel(string $reason, ?string $cancelledBy = null): void
    {
        $this->update([
            'status' => SaleStatus::CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        $this->statusHistories()->create([
            'status' => SaleStatus::CANCELLED->value,
            'comment' => $reason,
            'updated_by' => $cancelledBy ?? 'system',
        ]);
    }

    public function isCancellable(): bool
    {
        return $this->status->isCancellable();
    }

    public function isCompleted(): bool
    {
        return $this->status->isCompleted();
    }

    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatus::PAID;
    }

    public function recordPayment(float $amount, string $method, ?string $account = null, ?string $transactionId = null, ?string $notes = null): Payment
    {
        return $this->payments()->create([
            'user_id' => $this->user_id,
            'payment_number' => Payment::generatePaymentNumber(),
            'payment_type' => PaymentType::SALE->value,
            'customer_id' => $this->customer_id,
            'amount' => $amount,
            'payment_date' => now(),
            'payment_method' => $method,
            'payment_account' => $account,
            'transaction_id' => $transactionId,
            'notes' => $notes,
            'created_by' => $this->user_id,
        ]);
    }

    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}
