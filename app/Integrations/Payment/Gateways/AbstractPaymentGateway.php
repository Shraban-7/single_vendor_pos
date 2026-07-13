<?php

declare(strict_types=1);

namespace App\Integrations\Payment\Gateways;

use App\Integrations\Payment\Contracts\PaymentGatewayInterface;
use App\Integrations\Support\FormatsResponse;

/**
 * Shared behaviour for all payment gateways: credential access, sandbox
 * detection and the standardized response helpers. Concrete gateways only
 * implement the three contract methods.
 */
abstract class AbstractPaymentGateway implements PaymentGatewayInterface
{
    use FormatsResponse;

    /**
     * @param  array<string, mixed>  $credentials
     */
    public function __construct(protected array $credentials = [])
    {
    }

    protected function credential(string $key, mixed $default = null): mixed
    {
        return $this->credentials[$key] ?? $default;
    }

    protected function isSandbox(): bool
    {
        return (bool) ($this->credentials['sandbox'] ?? false);
    }
}
