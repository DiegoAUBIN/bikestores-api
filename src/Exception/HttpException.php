<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

/**
 * Represents an HTTP layer exception.
 */
class HttpException extends RuntimeException
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        private readonly int $statusCode,
        string $message,
        private readonly array $payload = []
    ) {
        parent::__construct($message, $statusCode);
    }

    /**
     * Returns the HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Returns the response payload.
     *
     * @return array<string, mixed>
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
}