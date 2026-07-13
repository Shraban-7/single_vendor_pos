<?php

declare(strict_types=1);

namespace App\Integrations\Courier;

use App\Integrations\Support\FormatsResponse;
use Throwable;

/**
 * The simple, public-facing API your ecommerce code should use.
 *
 *   $courier = app(CourierService::class);
 *   $response = $courier->createOrder('pathao', $shipmentData);
 */
class CourierService
{
    use FormatsResponse;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createOrder(string $provider, array $data): array
    {
        return $this->run(fn () => CourierManager::provider($provider)->createOrder($data));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function cancelOrder(string $provider, array $data): array
    {
        return $this->run(fn () => CourierManager::provider($provider)->cancelOrder($data));
    }

    /**
     * @return array<string, mixed>
     */
    public function trackOrder(string $provider, string $trackingCode): array
    {
        return $this->run(fn () => CourierManager::provider($provider)->trackOrder($trackingCode));
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
