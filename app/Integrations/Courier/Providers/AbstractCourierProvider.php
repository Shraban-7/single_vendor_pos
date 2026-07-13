<?php

declare(strict_types=1);

namespace App\Integrations\Courier\Providers;

use App\Integrations\Courier\Contracts\CourierProviderInterface;
use App\Integrations\Support\FormatsResponse;
use Illuminate\Support\Facades\Cache;

/**
 * Shared behaviour for all courier providers: credential access, sandbox
 * detection, standardized responses and short-lived access-token caching.
 */
abstract class AbstractCourierProvider implements CourierProviderInterface
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

    /**
     * Cache an access token so we do not re-authenticate on every request.
     * A null result is treated as a cache miss and will re-run next time.
     */
    protected function rememberToken(string $key, int $ttlSeconds, callable $callback): mixed
    {
        return Cache::remember('integrations.courier.token.'.$key, $ttlSeconds, $callback);
    }

    protected function forgetToken(string $key): void
    {
        Cache::forget('integrations.courier.token.'.$key);
    }
}
