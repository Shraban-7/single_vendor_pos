<?php

declare(strict_types=1);

namespace App\Integrations\Payment\Gateways;

use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * SSLCommerz hosted checkout.
 * Credentials: { "store_id": "...", "store_password": "...", "sandbox": true }
 *
 * Docs: https://developer.sslcommerz.com/
 */
class SSLCommerz extends AbstractPaymentGateway
{
    private function baseUrl(): string
    {
        return $this->isSandbox()
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }

    public function createPayment(array $data): array
    {
        try {
            $payload = [
                'store_id'         => $this->credential('store_id'),
                'store_passwd'     => $this->credential('store_password'),
                'total_amount'     => $data['amount'],
                'currency'         => $data['currency'] ?? 'BDT',
                'tran_id'          => $data['order_id'],
                'success_url'      => $data['success_url'],
                'fail_url'         => $data['fail_url'],
                'cancel_url'       => $data['cancel_url'],
                'cus_name'         => $data['customer_name'] ?? 'N/A',
                'cus_email'        => $data['customer_email'] ?? 'noemail@example.com',
                'cus_phone'        => $data['customer_phone'] ?? 'N/A',
                'cus_add1'         => $data['customer_address'] ?? 'N/A',
                'cus_city'         => $data['customer_city'] ?? 'Dhaka',
                'cus_country'      => $data['customer_country'] ?? 'Bangladesh',
                'shipping_method'  => 'NO',
                'product_name'     => $data['product_name'] ?? 'Order Payment',
                'product_category' => $data['product_category'] ?? 'General',
                'product_profile'  => 'general',
            ];

            $response = Http::asForm()->timeout(30)
                ->post($this->baseUrl().'/gwprocess/v4/api.php', $payload);

            $body = (array) $response->json();

            if ($response->successful() && ($body['status'] ?? '') === 'SUCCESS') {
                return $this->success('Payment session created successfully.', [
                    'redirect_url' => $body['GatewayPageURL'] ?? null,
                    'session_key'  => $body['sessionkey'] ?? null,
                ]);
            }

            return $this->failure(
                (string) ($body['failedreason'] ?? 'Unable to initiate SSLCommerz payment.')
            );
        } catch (Throwable $e) {
            return $this->failure('SSLCommerz request failed: '.$e->getMessage());
        }
    }

    public function verifyPayment(array $data): array
    {
        try {
            $response = Http::timeout(30)
                ->get($this->baseUrl().'/validator/api/validationserverAPI.php', [
                    'val_id'       => $data['val_id'],
                    'store_id'     => $this->credential('store_id'),
                    'store_passwd' => $this->credential('store_password'),
                    'format'       => 'json',
                ]);

            $body   = (array) $response->json();
            $status = (string) ($body['status'] ?? '');

            if ($response->successful() && in_array($status, ['VALID', 'VALIDATED'], true)) {
                return $this->success('Payment verified successfully.', [
                    'transaction_id' => $body['tran_id'] ?? null,
                    'amount'         => $body['amount'] ?? null,
                    'currency'       => $body['currency'] ?? null,
                    'bank_tran_id'   => $body['bank_tran_id'] ?? null,
                    'status'         => $status,
                ]);
            }

            return $this->failure('Payment verification failed or payment is invalid.', ['status' => $status]);
        } catch (Throwable $e) {
            return $this->failure('SSLCommerz verification failed: '.$e->getMessage());
        }
    }

    public function refundPayment(array $data): array
    {
        try {
            $response = Http::timeout(30)
                ->get($this->baseUrl().'/validator/api/merchantTransIDvalidationAPI.php', [
                    'bank_tran_id'   => $data['bank_tran_id'],
                    'refund_amount'  => $data['amount'],
                    'refund_remarks' => $data['reason'] ?? 'Customer refund',
                    'store_id'       => $this->credential('store_id'),
                    'store_passwd'   => $this->credential('store_password'),
                    'format'         => 'json',
                ]);

            $body = (array) $response->json();

            if ($response->successful() && ($body['status'] ?? '') === 'success') {
                return $this->success('Refund initiated successfully.', [
                    'refund_ref_id' => $body['refund_ref_id'] ?? null,
                    'status'        => $body['status'] ?? null,
                ]);
            }

            return $this->failure((string) ($body['errorReason'] ?? 'Refund request failed.'));
        } catch (Throwable $e) {
            return $this->failure('SSLCommerz refund failed: '.$e->getMessage());
        }
    }
}
