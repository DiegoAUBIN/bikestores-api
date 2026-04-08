<?php

declare(strict_types=1);

namespace App\Http;

use App\Exception\HttpException;

/**
 * Matches routes and dispatches controllers.
 */
class Router
{
    /**
     * @var array<int, array{method:string, pattern:string, handler:callable, middlewares:array<int, callable>}>
     */
    private array $routes = [];

    /**
     * Registers a route.
     *
     * @param array<int, callable> $middlewares
     */
    public function add(string $method, string $pattern, callable $handler, array $middlewares = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'handler' => $handler,
            'middlewares' => $middlewares,
        ];
    }

    /**
     * Dispatches the current request.
     */
    public function dispatch(Request $request): JsonResponse
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->getMethod()) {
                continue;
            }

            $routeParams = $this->match($route['pattern'], $request->getPath());

            if ($routeParams === null) {
                continue;
            }

            $resolvedRequest = $request->withRouteParams($routeParams);
            $handler = $route['handler'];
            $pipeline = array_reduce(
                array_reverse($route['middlewares']),
                static function (callable $next, callable $middleware): callable {
                    return static fn (Request $request): JsonResponse => $middleware($request, $next);
                },
                static fn (Request $request): JsonResponse => $handler($request)
            );

            return $pipeline($resolvedRequest);
        }

        throw new HttpException(404, 'Resource not found.', ['path' => $request->getPath()]);
    }

    /**
     * @return array<string, string>|null
     */
    private function match(string $pattern, string $path): ?array
    {
        $patternSegments = $this->splitPath($pattern);
        $pathSegments = $this->splitPath($path);

        if (count($patternSegments) !== count($pathSegments)) {
            return null;
        }

        $parameters = [];

        foreach ($patternSegments as $index => $patternSegment) {
            $pathSegment = $pathSegments[$index];

            if (preg_match('/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/', $patternSegment, $matches) === 1) {
                $parameters[$matches[1]] = $pathSegment;
                continue;
            }

            if ($patternSegment !== $pathSegment) {
                return null;
            }
        }

        return $parameters;
    }

    /**
     * @return array<int, string>
     */
    private function splitPath(string $path): array
    {
        $trimmedPath = trim($path, '/');

        if ($trimmedPath === '') {
            return [];
        }

        return explode('/', $trimmedPath);
    }
}