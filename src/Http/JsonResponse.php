<?php

declare(strict_types=1);

namespace App\Http;

/**
 * Represents a JSON HTTP response.
 */
class JsonResponse
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        private readonly array $payload,
        private readonly int $statusCode = 200
    ) {
    }

    /**
     * Creates a JSON response.
     *
     * @param array<string, mixed> $payload
     */
    public static function create(array $payload, int $statusCode = 200): self
    {
        return new self($payload, $statusCode);
    }

    /**
     * Sends the response to the client.
     */
    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($this->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}