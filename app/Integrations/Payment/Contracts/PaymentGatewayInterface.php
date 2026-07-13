<?php

declare(strict_types=1);

namespace App\Integrations\Payment\Contracts;

/**
 * Every payment gateway MUST implement this contract.
 * Implementing a new gateway is as simple as fulfilling these three methods.
 */
interface PaymentGatewayInterface
{
    /**
     * Initiate a payment session and (usually) return a redirect URL.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function createPayment(array $data): array;

    /**
     * Verify / validate a payment after the customer returns from the gateway.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function verifyPayment(array $data): array;

    /**
     * Refund a previously captured payment.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function refundPayment(array $data): array;
}
