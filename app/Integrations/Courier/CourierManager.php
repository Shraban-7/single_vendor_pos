<?php

declare(strict_types=1);

namespace App\Integrations\Courier;

use App\Integrations\Courier\Contracts\CourierProviderInterface;
use App\Models\CourierProvider as CourierProviderModel;
use InvalidArgumentException;

/**
 * Resolves a courier provider by slug:
 *   1. Loads active credentials from the database.
 *   2. Finds the mapped class in config/integrations.php.
 *   3. Instantiates it with the credentials injected.
 *
 * Usage: CourierManager::provider('pathao')->createOrder($data);
 */
class CourierManager
{
    public static function provider(string $slug): CourierProviderInterface
    {
        $record = CourierProviderModel::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if ($record === null) {
            throw new InvalidArgumentException(
                "Courier provider [{$slug}] is not configured or is inactive."
            );
        }

        /** @var class-string<CourierProviderInterface>|null $class */
        $class = config("integrations.courier.providers.{$slug}");

        if (! is_string($class) || ! class_exists($class)) {
            throw new InvalidArgumentException(
                "No courier provider class mapped for [{$slug}] in config/integrations.php."
            );
        }

        $provider = new $class((array) $record->credentials);

        if (! $provider instanceof CourierProviderInterface) {
            throw new InvalidArgumentException(
                "[{$class}] must implement CourierProviderInterface."
            );
        }

        return $provider;
    }
}
