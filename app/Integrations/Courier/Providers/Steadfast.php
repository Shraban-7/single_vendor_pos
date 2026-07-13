<?php

declare(strict_types=1);

namespace App\Integrations\Courier\Providers;

use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Steadfast Courier (Packzy API). Static API key + secret in headers.
 * Credentials: { "api_key": "...", "secret_key": "..." }
 *
 * Docs: https://docs.google.com/document/d/e/2PACX-steadfast-public-api
 */
class Steadfast extends AbstractCourierProvider
{
    private function baseUrl(): string
    {
        return 'https://portal.packzy.com/api/v1';
    }

    /**
     * @return array<string, string>
     */
    private function headers(): array
    {
        return [
            'Api-Key'      => (string) $this->credential('api_key'),
            'Secret-Key'   => (string) $this->credential('secret_key'),
            'Content-Type' => 'application/json',
        ];
    }

    public function createOrder(array $data): array
    {
        try {
            $response = Http::withHeaders($this->headers())->timeout(30)
                ->post($this->baseUrl().'/create_order', [
                    'invoice'           => $data['order_id'],
                    'recipient_name'    => $data['recipient_name'],
                    'recipient_phone'   => $data['recipient_phone'],
                    'recipient_address' => $data['recipient_address'],
                    'cod_amount'        => $data['cod_amount'] ?? 0,
                    'note'              => $data['note'] ?? '',
                ]);

            $body = (array) $response->json();

            if ($response->successful() && ((int) ($body['status'] ?? 0)) === 200) {
                $consignment = (array) ($body['consignment'] ?? []);

                return $this->success('Courier order created successfully.', [
                    'consignment_id' => $consignment['consignment_id'] ?? null,
                    'tracking_code'  => $consignment['tracking_code'] ?? null,
                    'order_status'   => $consignment['status'] ?? null,
                ]);
            }

            return $this->failure(
                (string) ($body['message'] ?? 'Unable to create Steadfast order.'),
                (array) ($body['errors'] ?? [])
            );
        } catch (Throwable $e) {
            return $this->failure('Steadfast request failed: '.$e->getMessage());
        }
    }

    public function cancelOrder(array $data): array
    {
        // Steadfast does not expose a public cancellation endpoint.
        return $this->failure('Steadfast does not support order cancellation via API. Contact Steadfast support.');
    }

    public function trackOrder(string $trackingCode): array
    {
        try {
            $response = Http::withHeaders($this->headers())->timeout(30)
                ->get($this->baseUrl().'/status_by_trackingcode/'.$trackingCode);

            $body = (array) $response->json();

            if ($response->successful() && array_key_exists('delivery_status', $body)) {
                return $this->success('Tracking information retrieved.', [
                    'tracking_code' => $trackingCode,
                    'status'        => $body['delivery_status'],
                ]);
            }

            return $this->failure((string) ($body['message'] ?? 'Unable to fetch tracking information.'));
        } catch (Throwable $e) {
            return $this->failure('Steadfast tracking failed: '.$e->getMessage());
        }
    }
}
