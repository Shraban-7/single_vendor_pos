<?php

declare(strict_types=1);

namespace App\Integrations\Courier\Contracts;

/**
 * Every courier provider MUST implement this contract.
 */
interface CourierProviderInterface
{
    /**
     * Create / book a delivery order.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createOrder(array $data): array;

    /**
     * Cancel a previously created delivery order.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function cancelOrder(array $data): array;

    /**
     * Track an order by its tracking / consignment code.
     *
     * @return array<string, mixed>
     */
    public function trackOrder(string $trackingCode): array;
}
