<?php

declare(strict_types=1);

namespace App\Integrations\Payment\Gateways;

use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * ShurjoPay (SurjoPay) hosted checkout. Uses a token-based auth flow.
 * Credentials: { "username": "...", "password": "...", "prefix": "sp", "sandbox": true }
 *
 * Docs: https://docs.shurjopay.com.bd/
 *
 * NOTE: confirm the LIVE host with ShurjoPay before going to production.
 */
class SurjoPay extends AbstractPaymentGateway
{
    private function baseUrl(): string
    {
        return $this->isSandbox()
            ? 'https://sandbox.shurjopayment.com'
            : 'https://engine.shurjopayment.com'; // PLACEHOLDER: confirm live host
    }

    /**
     * Authenticate and return the token payload (token, store_id, execute_url...).
     *
     * @return array<string, mixed>|null
     */
    private function authenticate(): ?array
    {
        $response = Http::asForm()->timeout(30)
            ->post($this->baseUrl().'/api/get_token', [
                'username' => $this->credential('username'),
                'password' => $this->credential('password'),
            ]);

        $body = (array) $response->json();

        return ($response->successful() && ! empty($body['token'])) ? $body : null;
    }

    public function createPayment(array $data): array
    {
        try {
            $auth = $this->authenticate();
            if ($auth === null) {
                return $this->failure('Unable to authenticate with ShurjoPay.');
            }

            $payload = [
                'token'              => $auth['token'],
                'store_id'           => $auth['store_id'] ?? null,
                'prefix'             => $this->credential('prefix', 'sp'),
                'amount'             => $data['amount'],
                'order_id'           => $data['order_id'],
                'currency'           => $data['currency'] ?? 'BDT',
                'customer_name'      => $data['customer_name'] ?? 'N/A',
                'customer_address'   => $data['customer_address'] ?? 'N/A',
                'customer_phone'     => $data['customer_phone'] ?? 'N/A',
                'customer_city'      => $data['customer_city'] ?? 'Dhaka',
                'customer_post_code' => $data['customer_post_code'] ?? '1200',
                'client_ip'          => $data['client_ip'] ?? request()->ip(),
                'return_url'         => $data['success_url'],
                'cancel_url'         => $data['cancel_url'],
            ];

            $executeUrl = $auth['execute_url'] ?? ($this->baseUrl().'/api/secret-pay');

            $response = Http::withToken((string) $auth['token'])->timeout(30)
                ->post((string) $executeUrl, $payload);

            $body = (array) $response->json();

            if ($response->successful() && ! empty($body['checkout_url'])) {
                return $this->success('Payment session created successfully.', [
                    'redirect_url' => $body['checkout_url'],
                    'sp_order_id'  => $body['sp_order_id'] ?? null,
                ]);
            }

            return $this->failure((string) ($body['message'] ?? 'Unable to initiate ShurjoPay payment.'));
        } catch (Throwable $e) {
            return $this->failure('ShurjoPay request failed: '.$e->getMessage());
        }
    }

    public function verifyPayment(array $data): array
    {
        try {
            $auth = $this->authenticate();
            if ($auth === null) {
                return $this->failure('Unable to authenticate with ShurjoPay.');
            }

            $response = Http::withToken((string) $auth['token'])->timeout(30)
                ->post($this->baseUrl().'/api/verification', [
                    'order_id' => $data['sp_order_id'] ?? $data['order_id'],
                ]);

            $body  = (array) $response->json();
            $first = (array) ($body[0] ?? []); // verification API returns a list

            if ($response->successful() && in_array((string) ($first['sp_code'] ?? ''), ['1000'], true)) {
                return $this->success('Payment verified successfully.', [
                    'transaction_id' => $first['bank_trx_id'] ?? null,
                    'amount'         => $first['amount'] ?? null,
                    'status'         => $first['sp_message'] ?? null,
                ]);
            }

            return $this->failure('Payment verification failed.', [
                'status' => $first['sp_message'] ?? 'unknown',
            ]);
        } catch (Throwable $e) {
            return $this->failure('ShurjoPay verification failed: '.$e->getMessage());
        }
    }

    public function refundPayment(array $data): array
    {
        // ShurjoPay has no public self-serve refund API; refunds go through support.
        return $this->failure(
            'ShurjoPay refunds must be handled through merchant support. No public refund API is available.'
        );
    }
}
