<?php

declare(strict_types=1);

namespace App\Integrations\Courier\Providers;

use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * RedX Open API. Uses a long-lived access token in the API-ACCESS-TOKEN header.
 * Credentials: { "access_token": "...", "sandbox": true }
 *
 * Docs: https://openapi.redx.com.bd/
 */
class RedX extends AbstractCourierProvider
{
    private function baseUrl(): string
    {
        return $this->isSandbox()
            ? 'https://sandbox.redx.com.bd/v1.0.0-beta'
            : 'https://openapi.redx.com.bd/v1.0.0-beta';
    }

    /**
     * @return array<string, string>
     */
    private function headers(): array
    {
        return [
            'API-ACCESS-TOKEN' => 'Bearer '.((string) $this->credential('access_token')),
            'Content-Type'     => 'application/json',
        ];
    }

    public function createOrder(array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers())->timeout(30)
                ->post($this->baseUrl().'/parcel', [
                    'customer_name'          => $data['recipient_name'],
                    'customer_phone'         => $data['recipient_phone'],
                    'delivery_area'          => $data['delivery_area'] ?? '',
                    'delivery_area_id'       => $data['delivery_area_id'] ?? null,
                    'customer_address'       => $data['recipient_address'],
                    'merchant_invoice_id'    => $data['order_id'],
                    'cash_collection_amount' => (string) ($data['cod_amount'] ?? '0'),
                    'parcel_weight'          => $data['weight'] ?? 500, // grams
                    'value'                  => $data['value'] ?? ($data['cod_amount'] ?? 0),
                ]);

            $body = (array) $response->json();

            if ($response->successful() && ! empty($body['tracking_id'])) {
                return $this->success('Courier order created successfully.', [
                    'tracking_code'  => $body['tracking_id'],
                    'consignment_id' => $body['tracking_id'],
                ]);
            }

            return $this->failure((string) ($body['message'] ?? 'Unable to create RedX parcel.'));
        } catch (Throwable $e) {
            return $this->failure('RedX request failed: '.$e->getMessage());
        }
    }

    public function cancelOrder(array $data): array
    {
        // RedX does not expose a public parcel-cancellation endpoint.
        return $this->failure('RedX does not support parcel cancellation via API. Cancel from the RedX panel.');
    }

    public function trackOrder(string $trackingCode): array
    {
        try {
            $response = Http::withHeaders($this->headers())->timeout(30)
                ->get($this->baseUrl().'/parcel/track/'.$trackingCode);

            $body = (array) $response->json();

            if ($response->successful()) {
                return $this->success('Tracking information retrieved.', [
                    'tracking_code' => $trackingCode,
                    'status'        => $body['tracking']['current_status'] ?? ($body['status'] ?? 'unknown'),
                ]);
            }

            return $this->failure('Unable to fetch tracking information.');
        } catch (Throwable $e) {
            return $this->failure('RedX tracking failed: '.$e->getMessage());
        }
    }
}
