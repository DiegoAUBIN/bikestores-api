<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\HttpException;
use App\Http\JsonResponse;
use App\Http\Request;

/**
 * Enforces authenticated employee session and optional role filtering.
 */
class SessionAuthMiddleware
{
    /**
     * @param array<int, string> $allowedRoles
     */
    public function __construct(private readonly array $allowedRoles = [])
    {
    }

    public function __invoke(Request $request, callable $next): JsonResponse
    {
        $employee = $_SESSION['employee'] ?? null;

        if (!is_array($employee) || !isset($employee['employee_id'], $employee['employee_role'], $employee['store_id'])) {
            throw new HttpException(401, 'Authentication required.');
        }

        if ($this->allowedRoles !== [] && !in_array((string) $employee['employee_role'], $this->allowedRoles, true)) {
            throw new HttpException(403, 'Insufficient permissions.');
        }

        return $next($request);
    }
}
