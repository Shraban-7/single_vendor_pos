<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CourierProvider;
use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

/**
 * OPTIONAL helper seeder. Run with:
 *   php artisan db:seed --class=Database\\Seeders\\IntegrationsSeeder
 *
 * Replace the "xxx" placeholders with your real sandbox/live credentials.
 * The "slug" of each row MUST match a key in config/integrations.php.
 */
class IntegrationsSeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            [
                'name'        => 'SSLCommerz',
                'slug'        => 'sslcommerz',
                'credentials' => ['store_id' => 'xxx', 'store_password' => 'xxx', 'sandbox' => true],
                'is_active'   => true,
            ],
            [
                'name'        => 'aamarPay',
                'slug'        => 'aamarpay',
                'credentials' => ['store_id' => 'xxx', 'signature_key' => 'xxx', 'sandbox' => true],
                'is_active'   => true,
            ],
            [
                'name'        => 'ShurjoPay',
                'slug'        => 'surjopay',
                'credentials' => ['username' => 'xxx', 'password' => 'xxx', 'prefix' => 'sp', 'sandbox' => true],
                'is_active'   => true,
            ],
            [
                'name'        => 'Cash on Delivery',
                'slug'        => 'cod',
                'credentials' => [],
                'is_active'   => true,
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::updateOrCreate(['slug' => $gateway['slug']], $gateway);
        }

        $providers = [
            [
                'name'        => 'Pathao',
                'slug'        => 'pathao',
                'credentials' => [
                    'client_id' => 'xxx', 'client_secret' => 'xxx',
                    'username'  => 'xxx', 'password' => 'xxx',
                    'store_id'  => 'xxx', 'sandbox' => true,
                ],
                'is_active'   => true,
            ],
            [
                'name'        => 'Steadfast',
                'slug'        => 'steadfast',
                'credentials' => ['api_key' => 'xxx', 'secret_key' => 'xxx'],
                'is_active'   => true,
            ],
            [
                'name'        => 'RedX',
                'slug'        => 'redx',
                'credentials' => ['access_token' => 'xxx', 'sandbox' => true],
                'is_active'   => true,
            ],
            [
                'name'        => 'eCourier',
                'slug'        => 'ecourier',
                'credentials' => ['api_key' => 'xxx', 'api_secret' => 'xxx', 'user_id' => 'xxx'],
                'is_active'   => true,
            ],
        ];

        foreach ($providers as $provider) {
            CourierProvider::updateOrCreate(['slug' => $provider['slug']], $provider);
        }
    }
}
