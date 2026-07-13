<?php

declare(strict_types=1);

namespace App\Integrations\Payment;

use App\Integrations\Support\FormatsResponse;
use Throwable;

/**
 * The simple, public-facing API your ecommerce code should use.
 *
 *   $payment = app(PaymentService::class);
 *   $response = $payment->create('sslcommerz', $orderData);
 *
 * Every method is guaranteed to return the standardized response array,
 * even when the underlying gateway throws.
 */
class PaymentService
{
    use FormatsResponse;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function create(string $gateway, array $data): array
    {
        return $this->run(fn () => PaymentManager::gateway($gateway)->createPayment($data));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function verify(string $gateway, array $data): array
    {
        return $this->run(fn () => PaymentManager::gateway($gateway)->verifyPayment($data));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function refund(string $gateway, array $data): array
    {
        return $this->run(fn () => PaymentManager::gateway($gateway)->refundPayment($data));
    }

    /**
     * @param  callable(): array<string, mixed>  $callback
     * @return array<string, mixed>
     */
    private function run(callable $callback): array
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            return $this->failure($e->getMessage());
        }
    }
}
