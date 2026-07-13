<?php

declare(strict_types=1);

namespace App\Integrations\Payment\Gateways;

use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * aamarPay hosted checkout.
 * Credentials: { "store_id": "...", "signature_key": "...", "sandbox": true }
 *
 * Docs: https://aamarpay.readme.io/
 */
class AamarPay extends AbstractPaymentGateway
{
    private function baseUrl(): string
    {
        return $this->isSandbox()
            ? 'https://sandbox.aamarpay.com'
            : 'https://secure.aamarpay.com';
    }

    public function createPayment(array $data): array
    {
        try {
            $payload = [
                'store_id'      => $this->credential('store_id'),
                'signature_key' => $this->credential('signature_key'),
                'amount'        => $data['amount'],
                'currency'      => $data['currency'] ?? 'BDT',
                'tran_id'       => $data['order_id'],
                'cus_name'      => $data['customer_name'] ?? 'N/A',
                'cus_email'     => $data['customer_email'] ?? 'noemail@example.com',
                'cus_phone'     => $data['customer_phone'] ?? 'N/A',
                'cus_add1'      => $data['customer_address'] ?? 'N/A',
                'cus_city'      => $data['customer_city'] ?? 'Dhaka',
                'cus_country'   => $data['customer_country'] ?? 'Bangladesh',
                'success_url'   => $data['success_url'],
                'fail_url'      => $data['fail_url'],
                'cancel_url'    => $data['cancel_url'],
                'desc'          => $data['description'] ?? 'Order Payment',
                'type'          => 'json',
            ];

            $response = Http::asForm()->timeout(30)
                ->post($this->baseUrl().'/jsonpost.php', $payload);

            $body = (array) $response->json();

            if ($response->successful() && ! empty($body['payment_url'])) {
                return $this->success('Payment session created successfully.', [
                    'redirect_url' => $body['payment_url'],
                ]);
            }

            return $this->failure(
                (string) ($body['reason'] ?? $body['cause'] ?? 'Unable to initiate aamarPay payment.')
            );
        } catch (Throwable $e) {
            return $this->failure('aamarPay request failed: '.$e->getMessage());
        }
    }

    public function verifyPayment(array $data): array
    {
        try {
            $response = Http::timeout(30)
                ->get($this->baseUrl().'/api/v1/trxcheck/request.php', [
                    'request_id'    => $data['transaction_id'] ?? $data['order_id'],
                    'store_id'      => $this->credential('store_id'),
                    'signature_key' => $this->credential('signature_key'),
                    'type'          => 'json',
                ]);

            $body = (array) $response->json();

            if ($response->successful() && ($body['pay_status'] ?? '') === 'Successful') {
                return $this->success('Payment verified successfully.', [
                    'transaction_id' => $body['pg_txnid'] ?? null,
                    'amount'         => $body['amount'] ?? null,
                    'status'         => $body['pay_status'] ?? null,
                ]);
            }

            return $this->failure('Payment verification failed.', [
                'status' => $body['pay_status'] ?? 'unknown',
            ]);
        } catch (Throwable $e) {
            return $this->failure('aamarPay verification failed: '.$e->getMessage());
        }
    }

    public function refundPayment(array $data): array
    {
        // aamarPay does not expose a public programmatic refund API.
        // Refunds are processed from the merchant panel.
        return $this->failure(
            'aamarPay does not support automated refunds via API. Process the refund from the merchant panel.'
        );
    }
}
