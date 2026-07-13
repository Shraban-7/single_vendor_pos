<?php

declare(strict_types=1);

namespace App\Integrations\Courier\Providers;

use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Pathao Courier (Merchant API). OAuth password-grant token, cached for ~1h.
 * Credentials: {
 *   "client_id": "...", "client_secret": "...",
 *   "username": "...", "password": "...",
 *   "store_id": "...", "sandbox": true
 * }
 *
 * Docs: https://merchant.pathao.com/courier/developer-api
 */
class Pathao extends AbstractCourierProvider
{
    private function baseUrl(): string
    {
        return $this->isSandbox()
            ? 'https://courier-api-sandbox.pathao.com'
            : 'https://api-hermes.pathao.com';
    }

    private function accessToken(): ?string
    {
        $cacheKey = 'pathao_'.md5((string) $this->credential('client_id'));

        $token = $this->rememberToken($cacheKey, 3000, function (): ?string {
            $response = Http::timeout(30)
                ->post($this->baseUrl().'/aladdin/api/v1/issue-token', [
                    'client_id'     => $this->credential('client_id'),
                    'client_secret' => $this->credential('client_secret'),
                    'grant_type'    => 'password',
                    'username'      => $this->credential('username'),
                    'password'      => $this->credential('password'),
                ]);

            return $response->successful() ? $response->json('access_token') : null;
        });

        return is_string($token) ? $token : null;
    }

    public function createOrder(array $data): array
    {
        try {
            $token = $this->accessToken();
            if ($token === null) {
                return $this->failure('Unable to authenticate with Pathao.');
            }

            $response = Http::withToken($token)->acceptJson()->timeout(30)
                ->post($this->baseUrl().'/aladdin/api/v1/orders', [
                    'store_id'          => $this->credential('store_id'),
                    'merchant_order_id' => $data['order_id'],
                    'recipient_name'    => $data['recipient_name'],
                    'recipient_phone'   => $data['recipient_phone'],
                    'recipient_address' => $data['recipient_address'],
                    'recipient_city'    => $data['city_id'] ?? null,
                    'recipient_zone'    => $data['zone_id'] ?? null,
                    'recipient_area'    => $data['area_id'] ?? null,
                    'delivery_type'     => $data['delivery_type'] ?? 48,  // 48 = Normal, 12 = On Demand
                    'item_type'         => $data['item_type'] ?? 2,       // 2 = Parcel, 1 = Document
                    'item_quantity'     => $data['quantity'] ?? 1,
                    'item_weight'       => $data['weight'] ?? 0.5,
                    'amount_to_collect' => $data['cod_amount'] ?? 0,
                    'item_description'  => $data['description'] ?? '',
                ]);

            $body = (array) $response->json();

            if ($response->successful() && ($body['type'] ?? '') === 'success') {
                $consignment = (array) ($body['data'] ?? []);

                return $this->success('Courier order created successfully.', [
                    'consignment_id' => $consignment['consignment_id'] ?? null,
                    'tracking_code'  => $consignment['consignment_id'] ?? null,
                    'order_status'   => $consignment['order_status'] ?? null,
                ]);
            }

            return $this->failure(
                (string) ($body['message'] ?? 'Unable to create Pathao order.'),
                (array) ($body['errors'] ?? [])
            );
        } catch (Throwable $e) {
            return $this->failure('Pathao request failed: '.$e->getMessage());
        }
    }

    public function cancelOrder(array $data): array
    {
        // Pathao does not currently expose a public order-cancellation endpoint.
        return $this->failure('Pathao does not support order cancellation via API. Cancel from the merchant panel.');
    }

    public function trackOrder(string $trackingCode): array
    {
        try {
            $token = $this->accessToken();
            if ($token === null) {
                return $this->failure('Unable to authenticate with Pathao.');
            }

            // PLACEHOLDER: confirm the exact tracking endpoint with Pathao for your account.
            $response = Http::withToken($token)->acceptJson()->timeout(30)
                ->get($this->baseUrl().'/aladdin/api/v1/orders/'.$trackingCode.'/info');

            $body = (array) $response->json();

            if ($response->successful()) {
                return $this->success('Tracking information retrieved.', [
                    'tracking_code' => $trackingCode,
                    'status'        => $body['data']['order_status'] ?? 'unknown',
                ]);
            }

            return $this->failure('Unable to fetch tracking information.');
        } catch (Throwable $e) {
            return $this->failure('Pathao tracking failed: '.$e->getMessage());
        }
    }
}
