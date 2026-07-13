<?php

declare(strict_types=1);

namespace App\Integrations\Payment\Gateways;

/**
 * Cash on Delivery — a local, no-API gateway so COD flows through the exact
 * same PaymentService API as the online gateways. No credentials required.
 */
class CashOnDelivery extends AbstractPaymentGateway
{
    public function createPayment(array $data): array
    {
        return $this->success('Cash on delivery order placed. Payment will be collected on delivery.', [
            'order_id' => $data['order_id'] ?? null,
            'amount'   => $data['amount'] ?? null,
            'method'   => 'cod',
            'status'   => 'pending',
        ]);
    }

    public function verifyPayment(array $data): array
    {
        // COD is "verified" manually once cash is collected on delivery.
        return $this->success('Cash on delivery payment marked as collected.', [
            'order_id' => $data['order_id'] ?? null,
            'status'   => $data['status'] ?? 'collected',
        ]);
    }

    public function refundPayment(array $data): array
    {
        // Recorded for bookkeeping; the actual cash is settled with the customer.
        return $this->success('Cash on delivery refund recorded. Settle the amount with the customer manually.', [
            'order_id' => $data['order_id'] ?? null,
            'amount'   => $data['amount'] ?? null,
            'status'   => 'refunded',
        ]);
    }
}
