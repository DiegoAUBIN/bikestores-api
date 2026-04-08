<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\HttpException;
use App\Http\JsonResponse;
use App\Http\Request;

/**
 * Validates the API access key for write operations.
 */
class ApiKeyMiddleware
{
    public function __construct(private readonly string $expectedApiKey)
    {
    }

    /**
     * Validates the request API key.
     */
    public function __invoke(Request $request, callable $next): JsonResponse
    {
        if ($request->getMethod() === 'GET') {
            return $next($request);
        }

        $providedApiKey = $request->getHeader('X-API-KEY') ?? $request->getQueryParam('api_key');

        if ($providedApiKey !== $this->expectedApiKey) {
            throw new HttpException(401, 'Invalid or missing API key.');
        }

        return $next($request);
    }
}