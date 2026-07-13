<?php

declare(strict_types=1);

namespace App\Integrations\Support;

/**
 * Guarantees every gateway, provider and service returns the same shape.
 * This is the single source of truth for the standardized response format,
 * so we never duplicate the array structure across the codebase.
 */
trait FormatsResponse
{
    /**
     * @param  array<string, mixed>  $data
     * @return array{success: bool, message: string, data: array<string, mixed>}
     */
    protected function success(string $message, array $data = []): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ];
    }

    /**
     * @param  array<int|string, mixed>  $errors
     * @return array{success: bool, message: string, errors: array<int|string, mixed>}
     */
    protected function failure(string $message, array $errors = []): array
    {
        return [
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ];
    }
}
