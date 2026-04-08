<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Employee;
use App\Exception\HttpException;
use App\Http\JsonResponse;
use App\Http\Request;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Handles authentication endpoints.
 */
class AuthController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * Authenticates an employee with email and password.
     */
    public function login(Request $request): JsonResponse
    {
        $payload = $request->getParsedBody();

        if (!isset($payload['email'], $payload['password'])) {
            throw new HttpException(422, 'The email and password fields are required.');
        }

        $email = (string) $payload['email'];
        $password = (string) $payload['password'];

        /** @var Employee|null $employee */
        $employee = $this->entityManager->getRepository(Employee::class)->findOneBy(['employeeEmail' => $email]);

        if ($employee === null || $employee->getEmployeePassword() !== $password) {
            throw new HttpException(401, 'Invalid credentials.');
        }

        $_SESSION['employee'] = [
            'employee_id' => $employee->getEmployeeId(),
            'store_id' => $employee->getStore()?->getStoreId(),
            'employee_name' => $employee->getEmployeeName(),
            'employee_email' => $employee->getEmployeeEmail(),
            'employee_role' => $employee->getEmployeeRole(),
        ];

        return JsonResponse::create([
            'message' => 'Authentication succeeded.',
            'employee' => $_SESSION['employee'],
        ]);
    }

    /**
     * Returns the authenticated employee session.
     */
    public function me(Request $request): JsonResponse
    {
        $employee = $_SESSION['employee'] ?? null;

        if (!is_array($employee)) {
            throw new HttpException(401, 'Authentication required.');
        }

        return JsonResponse::create([
            'employee' => $employee,
        ]);
    }

    /**
     * Updates authenticated employee profile (name/email/password).
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $sessionEmployee = $_SESSION['employee'] ?? null;

        if (!is_array($sessionEmployee) || !isset($sessionEmployee['employee_id'])) {
            throw new HttpException(401, 'Authentication required.');
        }

        /** @var Employee|null $employee */
        $employee = $this->entityManager->find(Employee::class, (int) $sessionEmployee['employee_id']);

        if ($employee === null) {
            throw new HttpException(404, 'Employee not found.');
        }

        $payload = $request->getParsedBody();

        if ($payload === []) {
            throw new HttpException(400, 'A JSON payload is required.');
        }

        if (array_key_exists('employee_name', $payload)) {
            $employee->setEmployeeName((string) $payload['employee_name']);
        }

        if (array_key_exists('employee_email', $payload)) {
            $employee->setEmployeeEmail((string) $payload['employee_email']);
        }

        if (array_key_exists('employee_password', $payload)) {
            $employee->setEmployeePassword((string) $payload['employee_password']);
        }

        $this->entityManager->flush();

        $_SESSION['employee'] = [
            'employee_id' => $employee->getEmployeeId(),
            'store_id' => $employee->getStore()?->getStoreId(),
            'employee_name' => $employee->getEmployeeName(),
            'employee_email' => $employee->getEmployeeEmail(),
            'employee_role' => $employee->getEmployeeRole(),
        ];

        return JsonResponse::create([
            'message' => 'Profile updated successfully.',
            'employee' => $_SESSION['employee'],
        ]);
    }

    /**
     * Ends authenticated session.
     */
    public function logout(Request $request): JsonResponse
    {
        unset($_SESSION['employee']);
        session_regenerate_id(true);

        return JsonResponse::create([
            'message' => 'Logged out successfully.',
        ]);
    }
}