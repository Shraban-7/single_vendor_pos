<?php

declare(strict_types=1);

namespace App\Integrations\Payment;

use App\Integrations\Payment\Contracts\PaymentGatewayInterface;
use App\Models\PaymentGateway as PaymentGatewayModel;
use InvalidArgumentException;

/**
 * Resolves a payment gateway by slug:
 *   1. Loads active credentials from the database.
 *   2. Finds the mapped class in config/integrations.php.
 *   3. Instantiates it with the credentials injected.
 *
 * Usage: PaymentManager::gateway('sslcommerz')->createPayment($data);
 */
class PaymentManager
{
    public static function gateway(string $slug): PaymentGatewayInterface
    {
        $record = PaymentGatewayModel::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if ($record === null) {
            throw new InvalidArgumentException(
                "Payment gateway [{$slug}] is not configured or is inactive."
            );
        }

        /** @var class-string<PaymentGatewayInterface>|null $class */
        $class = config("integrations.payment.gateways.{$slug}");

        if (! is_string($class) || ! class_exists($class)) {
            throw new InvalidArgumentException(
                "No payment gateway class mapped for [{$slug}] in config/integrations.php."
            );
        }

        $gateway = new $class((array) $record->credentials);

        if (! $gateway instanceof PaymentGatewayInterface) {
            throw new InvalidArgumentException(
                "[{$class}] must implement PaymentGatewayInterface."
            );
        }

        return $gateway;
    }
}
