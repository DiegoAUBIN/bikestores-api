<?php

declare(strict_types=1);

namespace App\Http;

/**
 * Represents the current HTTP request.
 */
class Request
{
    /**
     * @param array<string, string> $headers
     * @param array<string, string> $queryParams
     * @param array<string, mixed> $parsedBody
     * @param array<string, string> $routeParams
     */
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $headers,
        private readonly array $queryParams,
        private readonly array $parsedBody,
        private readonly array $routeParams = []
    ) {
    }

    /**
     * Creates a request object from PHP globals.
     */
    public static function fromGlobals(string $basePath = ''): self
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $normalizedBasePath = rtrim($basePath, '/');
        $path = $requestUri;

        if ($normalizedBasePath !== '' && str_starts_with($requestUri, $normalizedBasePath)) {
            $path = substr($requestUri, strlen($normalizedBasePath)) ?: '/';
        }

        $headers = self::collectHeaders();
        $rawBody = file_get_contents('php://input') ?: '';
        $parsedBody = [];
        $contentType = strtolower($headers['content-type'] ?? '');

        if ($rawBody !== '' && str_contains($contentType, 'application/json')) {
            $decodedBody = json_decode($rawBody, true);

            if (is_array($decodedBody)) {
                $parsedBody = $decodedBody;
            }
        }

        return new self(
            method: strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            path: '/' . trim($path, '/'),
            headers: $headers,
            queryParams: array_map('strval', $_GET),
            parsedBody: $parsedBody,
        );
    }

    /**
     * Returns a copy with route parameters attached.
     *
     * @param array<string, string> $routeParams
     */
    public function withRouteParams(array $routeParams): self
    {
        return new self($this->method, $this->path, $this->headers, $this->queryParams, $this->parsedBody, $routeParams);
    }

    /**
     * Returns the HTTP method.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Returns the normalized request path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns a header value.
     */
    public function getHeader(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    /**
     * Returns a query parameter value.
     */
    public function getQueryParam(string $name): ?string
    {
        return $this->queryParams[$name] ?? null;
    }

    /**
     * Returns a route parameter value.
     */
    public function getRouteParam(string $name): ?string
    {
        return $this->routeParams[$name] ?? null;
    }

    /**
     * Returns the parsed JSON payload.
     *
     * @return array<string, mixed>
     */
    public function getParsedBody(): array
    {
        return $this->parsedBody;
    }

    /**
     * Returns whether the request has a JSON payload.
     */
    public function hasParsedBody(): bool
    {
        return $this->parsedBody !== [];
    }

    /**
     * @return array<string, string>
     */
    private static function collectHeaders(): array
    {
        $headers = [];

        if (function_exists('getallheaders')) {
            foreach (getallheaders() as $name => $value) {
                $headers[strtolower((string) $name)] = (string) $value;
            }

            return $headers;
        }

        foreach ($_SERVER as $key => $value) {
            if (!str_starts_with($key, 'HTTP_')) {
                continue;
            }

            $name = strtolower(str_replace('_', '-', substr($key, 5)));
            $headers[$name] = (string) $value;
        }

        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] = (string) $_SERVER['CONTENT_TYPE'];
        }

        return $headers;
    }
}