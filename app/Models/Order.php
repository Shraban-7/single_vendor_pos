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

    protected static function booted()
    {
        static::created(function ($order) {
            $admins = \App\Models\User::whereIn('role', \App\Enums\UserRole::staffRoles())->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'New Order Received',
                    'message' => "Order {$order->order_number} has been placed.",
                    'type' => \App\Enums\SystemNotificationType::ORDER_CREATED,
                    'action_url' => route('admin.orders.show', $order->order_number),
                ]);
            }
        });

        static::updated(function ($order) {
            if ($order->wasChanged('status')) {
                $status = $order->status;
                $notificationType = match ($status) {
                    \App\Enums\OrderStatus::PENDING =>\App\Enums\SystemNotificationType::ORDER_CREATED,
                    \App\Enums\OrderStatus::CONFIRMED =>\App\Enums\SystemNotificationType::ORDER_CONFIRMED,
                    \App\Enums\OrderStatus::SHIPPED => \App\Enums\SystemNotificationType::ORDER_SHIPPED,
                    \App\Enums\OrderStatus::DELIVERED => \App\Enums\SystemNotificationType::ORDER_DELIVERED,
                    \App\Enums\OrderStatus::CANCELLED => \App\Enums\SystemNotificationType::ORDER_CANCELLED,
                    default => null,
                };

                if (!$notificationType) {
                    return;
                }

                $admins = \App\Models\User::whereIn('role',\App\Enums\UserRole::staffRoles())->get();

                foreach ($admins as $admin) {
                    \App\Models\Notification::create([
                        'user_id' => $admin->id,
                        'title' => 'Order Status Updated',
                        'message' => "Order {$order->order_number} status changed to {$status->value}.",
                        'type' => $notificationType,
                        'action_url' => route('admin.orders.show', $order->order_number),
                    ]);
                }
            }
        });
    }

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
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class,'employee_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class,'shipping_district');
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
    public static function generateOrderNumber($prefix = 'SF'): string
    {
        $date = now()->format('ymd');

        $orderNumber = 0;

        if ($prefix === 'SR') {
            $lastReturn = SaleReturn::whereDate('created_at', today())
                ->latest()
                ->first();

            $orderNumber = $lastReturn ? ((int) substr($lastReturn->returned_id, -2)) + 1 : 1;
        } else {
            $lastOrder = Order::withTrashed()
                ->whereDate('created_at', today())
                ->latest()
                ->first();
            $orderNumber = $lastOrder ? ((int) substr($lastOrder->order_number, -2)) + 1 : 1;
        }

        $sequence = $orderNumber;

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
