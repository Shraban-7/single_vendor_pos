<?php

namespace App\Models;

use App\Enums\DeliveryZone;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    // protected $fillable = [
    //     'order_number',
    //     'user_id',
    //     'coupon_id',
    //     'status',
    //     'payment_method',
    //     'payment_status',
    //     'transaction_id',
    //     'paid_at',
    //     'subtotal',
    //     'shipping_cost',
    //     'discount_amount',
    //     'tax_amount',
    //     'total',
    //     'shipping_name',
    //     'shipping_phone',
    //     'shipping_email',
    //     'shipping_district',
    //     'shipping_city',
    //     'shipping_address',
    //     'shipping_postal_code',
    //     'delivery_zone',
    //     'notes',
    //     'admin_notes',
    //     'tracking_number',
    //     'courier',
    //     'confirmed_at',
    //     'shipped_at',
    //     'delivered_at',
    //     'cancelled_at',
    //     'cancellation_reason',
    // ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'status' => OrderStatus::class,
        'payment_method' => PaymentMethod::class,
        'payment_status' => PaymentStatus::class,
        'delivery_zone' => DeliveryZone::class,
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderByDesc('created_at');
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    // Scopes
    public function scopeByStatus($query, OrderStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', OrderStatus::PENDING);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', OrderStatus::activeStatuses());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', OrderStatus::DELIVERED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', OrderStatus::CANCELLED);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', PaymentStatus::PAID);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', PaymentStatus::PENDING);
    }

    // Helpers
    public static function generateOrderNumber(): string
    {
        $prefix = 'SF';
        
        $date = now()->format('ymd');
        
        $lastOrder = Order::withTrashed()
            ->whereDate('created_at', today())
            ->latest()
            ->first();

        $sequence = $lastOrder ? ((int) substr($lastOrder->order_number, -2)) + 1 : 1;

        return $prefix . $date . str_pad($sequence, 2, '0', STR_PAD_LEFT);
    }

    public function updateStatus(OrderStatus $status, ?string $comment = null, ?string $updatedBy = null): void
    {
        $this->update(['status' => $status]);

        // Set timestamps based on status
        match ($status) {
            OrderStatus::CONFIRMED => $this->update(['confirmed_at' => now()]),
            OrderStatus::SHIPPED => $this->update(['shipped_at' => now()]),
            OrderStatus::DELIVERED => $this->update(['delivered_at' => now()]),
            OrderStatus::CANCELLED => $this->update(['cancelled_at' => now()]),
            default => null,
        };

        // Create status history
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
            'status' => OrderStatus::CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        $this->statusHistories()->create([
            'status' => OrderStatus::CANCELLED->value,
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

    public function isGuest(): bool
    {
        return is_null($this->user_id);
    }

    public function getItemsCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function getFullShippingAddressAttribute(): string
    {
        return implode(', ', array_filter([
            $this->shipping_address,
            $this->shipping_city,
            $this->shipping_district,
            $this->shipping_postal_code,
        ]));
    }
}
