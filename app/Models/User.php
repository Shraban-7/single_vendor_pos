<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Gender;
use App\Enums\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    const ACTIVITY_TIMEOUT = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'avatar',
        'gender',
        'date_of_birth',
        'is_active',
        'is_phone_verified',
        'phone_verified_at',
        'last_login_at',
        'last_login_ip',
        'last_seen',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_seen' => 'datetime',
        'last_login' => 'datetime',
        'last_login_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'is_phone_verified' => 'boolean',
        'role' => UserRole::class,
        'gender' => Gender::class,
    ];

    // Relationships
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', UserRole::CUSTOMER);
    }

    public function scopeStaff($query)
    {
        return $query->whereIn('role', UserRole::staffRoles());
    }

    public function scopeOnline($query)
    {
        return $query->where('last_seen', '>=', Carbon::now()->subMinutes($this::ACTIVITY_TIMEOUT));
    }

    // Helpers
    public function getIsOnlineAttribute()
    {
        return $this->last_seen && $this->last_seen->gt(Carbon::now()->subMinutes($this::ACTIVITY_TIMEOUT));
    }

    public function isAdmin(): bool
    {
        return $this->role->isAdmin();
    }

    public function isCustomer(): bool
    {
        return $this->role === UserRole::CUSTOMER;
    }

    public function canAccessDashboard(): bool
    {
        return $this->role->canAccessDashboard();
    }

    public function hasProductInWishlist(int $productId): bool
    {
        return $this->wishlists()->where('product_id', $productId)->exists();
    }

    public function getOrCreateCart(): Cart
    {
        return $this->cart ?? $this->cart()->create();
    }

    public function getWishlistCountAttribute(): int
    {
        return $this->wishlists()->count();
    }

    public function getTotalOrdersAttribute(): int
    {
        return $this->orders()->count();
    }

    public function getTotalSpentAttribute(): float
    {
        return $this->orders()
            ->where('payment_status', 'paid')
            ->sum('total');
    }

    public function recordLogin(?string $ip = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }
}
