<?php

declare(strict_types=1);

namespace App\Integrations\Courier\Providers;

use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * eCourier API. Auth via API-KEY / API-SECRET / USER-ID headers.
 * Credentials: { "api_key": "...", "api_secret": "...", "user_id": "..." }
 *
 * Docs: https://docs.ecourier.com.bd/
 *
 * NOTE: class is named "Ecourier" (config slug "ecourier") for PSR-4 cleanliness.
 */
class Ecourier extends AbstractCourierProvider
{
    private function baseUrl(): string
    {
        return 'https://backoffice.ecourier.com.bd/api';
    }

    /**
     * @return array<string, string>
     */
    private function headers(): array
    {
        return [
            'API-KEY'      => (string) $this->credential('api_key'),
            'API-SECRET'   => (string) $this->credential('api_secret'),
            'USER-ID'      => (string) $this->credential('user_id'),
            'Content-Type' => 'application/json',
        ];
    }

    public function createOrder(array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers())->timeout(30)
                ->post($this->baseUrl().'/order-place', [
                    'recipient_name'    => $data['recipient_name'],
                    'recipient_mobile'  => $data['recipient_phone'],
                    'recipient_city'    => $data['city'] ?? '',
                    'recipient_area'    => $data['area'] ?? '',
                    'recipient_thana'   => $data['thana'] ?? '',
                    'recipient_zip'     => $data['zip'] ?? '',
                    'recipient_address' => $data['recipient_address'],
                    'package_code'      => $data['package_code'] ?? 'BD',
                    'product_price'     => $data['cod_amount'] ?? 0,
                    'payment_method'    => $data['payment_method'] ?? 'COD',
                    'comments'          => $data['note'] ?? '',
                    'number_of_item'    => $data['quantity'] ?? 1,
                    'product_id'        => $data['order_id'],
                ]);

            $body = (array) $response->json();

            $created = ($body['status'] ?? '') === 'success' || ! empty($body['ID']) || ! empty($body['tracking']);

            if ($response->successful() && $created) {
                return $this->success('Courier order created successfully.', [
                    'tracking_code'  => $body['ID'] ?? $body['tracking'] ?? null,
                    'consignment_id' => $body['ID'] ?? null,
                ]);
            }

            return $this->failure((string) ($body['message'] ?? 'Unable to create eCourier order.'));
        } catch (Throwable $e) {
            return $this->failure('eCourier request failed: '.$e->getMessage());
        }
    }

    public function cancelOrder(array $data): array
    {
        // eCourier does not expose a public cancellation endpoint.
        return $this->failure('eCourier does not support order cancellation via API. Contact eCourier support.');
    }

    public function trackOrder(string $trackingCode): array
    {
        try {
            $response = Http::withHeaders($this->headers())->timeout(30)
                ->post($this->baseUrl().'/track', [
                    'product_id' => $trackingCode,
                ]);

            $body = (array) $response->json();

            if ($response->successful()) {
                return $this->success('Tracking information retrieved.', [
                    'tracking_code' => $trackingCode,
                    'status'        => $body['data']['status'] ?? ($body['status'] ?? 'unknown'),
                ]);
            }

            return $this->failure('Unable to fetch tracking information.');
        } catch (Throwable $e) {
            return $this->failure('eCourier tracking failed: '.$e->getMessage());
        }
    }
}
