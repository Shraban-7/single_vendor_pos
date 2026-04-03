<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Enums\CouponType;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome Discount',
                'description' => 'Get 10% off on your first order',
                'type' => CouponType::PERCENTAGE,
                'value' => 10,
                'minimum_order_amount' => 1000,
                'maximum_discount' => 500,
                'usage_limit' => null,
                'usage_limit_per_user' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addYear(),
                'is_active' => true,
            ],
            [
                'code' => 'FLAT200',
                'name' => 'Flat 200 Off',
                'description' => 'Get flat ৳200 off on orders above ৳2000',
                'type' => CouponType::FIXED,
                'value' => 200,
                'minimum_order_amount' => 2000,
                'maximum_discount' => null,
                'usage_limit' => 100,
                'usage_limit_per_user' => 2,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'SUMMER25',
                'name' => 'Summer Sale',
                'description' => 'Get 25% off on summer collection',
                'type' => CouponType::PERCENTAGE,
                'value' => 25,
                'minimum_order_amount' => 1500,
                'maximum_discount' => 1000,
                'usage_limit' => 500,
                'usage_limit_per_user' => 3,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(2),
                'is_active' => true,
            ],
            [
                'code' => 'FREESHIP',
                'name' => 'Free Shipping',
                'description' => 'Free shipping on all orders (৳120 off)',
                'type' => CouponType::FIXED,
                'value' => 120,
                'minimum_order_amount' => 500,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_limit_per_user' => 5,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'code' => 'EID50',
                'name' => 'Eid Special',
                'description' => 'Eid special 50% off (max ৳2000)',
                'type' => CouponType::PERCENTAGE,
                'value' => 50,
                'minimum_order_amount' => 3000,
                'maximum_discount' => 2000,
                'usage_limit' => 200,
                'usage_limit_per_user' => 1,
                'starts_at' => now(),
                'expires_at' => now()->addMonth(),
                'is_active' => false, // Inactive until Eid
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::updateOrCreate(
                ['code' => $coupon['code']],
                $coupon
            );
        }
    }
}
