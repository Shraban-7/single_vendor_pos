<?php

declare(strict_types=1);

use App\Integrations\Courier\Providers\Ecourier;
use App\Integrations\Courier\Providers\Pathao;
use App\Integrations\Courier\Providers\RedX;
use App\Integrations\Courier\Providers\Steadfast;
use App\Integrations\Payment\Gateways\AamarPay;
use App\Integrations\Payment\Gateways\CashOnDelivery;
use App\Integrations\Payment\Gateways\SSLCommerz;
use App\Integrations\Payment\Gateways\SurjoPay;

return [

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways
    |--------------------------------------------------------------------------
    | Map a gateway "slug" (matches payment_gateways.slug in the database)
    | to the class that implements PaymentGatewayInterface.
    |
    | To ADD a gateway:    create the class + add one line here + add a DB row.
    | To REMOVE a gateway: delete the class + remove the line + deactivate row.
    */
    'payment' => [
        'gateways' => [
            'sslcommerz' => SSLCommerz::class,
            'aamarpay'   => AamarPay::class,
            'surjopay'   => SurjoPay::class,
            'cod'        => CashOnDelivery::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Courier Providers
    |--------------------------------------------------------------------------
    | Map a provider "slug" (matches courier_providers.slug in the database)
    | to the class that implements CourierProviderInterface.
    */
    'courier' => [
        'providers' => [
            'pathao'    => Pathao::class,
            'steadfast' => Steadfast::class,
            'redx'      => RedX::class,
            'ecourier'  => Ecourier::class,
        ],
    ],

];
