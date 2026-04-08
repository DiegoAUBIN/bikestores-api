<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Employee;
use App\Entity\Store;
use App\Exception\HttpException;
use App\Http\JsonResponse;
use App\Http\Request;

/**
 * Handles employee API requests.
 */
class EmployeeController extends AbstractCrudController
{
    public function index(Request $request): JsonResponse
    {
        $sessionEmployee = $this->requireSessionEmployee();
        $role = (string) $sessionEmployee['employee_role'];

        if ($role === 'employee') {
            throw new HttpException(403, 'Insufficient permissions.');
        }

        if ($role === 'chief') {
            $items = array_map(
                fn (object $entity): array => $this->serializeEntity($entity),
                $this->repository()->findBy(['store' => (int) $sessionEmployee['store_id']])
            );

            return JsonResponse::create([
                'resource' => $this->getResourceName(),
                'count' => count($items),
                'items' => $items,
            ]);
        }

        return parent::index($request);
    }

    public function store(Request $request): JsonResponse
    {
        $sessionEmployee = $this->requireSessionEmployee();
        $role = (string) $sessionEmployee['employee_role'];

        if (!in_array($role, ['chief', 'it'], true)) {
            throw new HttpException(403, 'Insufficient permissions.');
        }

        $payload = $this->requirePayload($request);

        if (!array_key_exists('store_id', $payload)) {
            throw new HttpException(422, 'The store_id field is required.');
        }

        if ($role === 'chief' && (int) $payload['store_id'] !== (int) $sessionEmployee['store_id']) {
            throw new HttpException(403, 'Chief users can only create employees in their own store.');
        }

        if ($role === 'it') {
            $newRole = (string) ($payload['employee_role'] ?? '');

            if (!in_array($newRole, ['employee', 'chief'], true)) {
                throw new HttpException(422, 'IT can only create employees with role employee or chief.');
            }
        }

        return parent::store($request);
    }

    public function update(Request $request): JsonResponse
    {
        $sessionEmployee = $this->requireSessionEmployee();

        if ((string) $sessionEmployee['employee_role'] !== 'it') {
            throw new HttpException(403, 'Only IT can update employees.');
        }

        return parent::update($request);
    }

    public function destroy(Request $request): JsonResponse
    {
        $sessionEmployee = $this->requireSessionEmployee();

        if ((string) $sessionEmployee['employee_role'] !== 'it') {
            throw new HttpException(403, 'Only IT can delete employees.');
        }

        return parent::destroy($request);
    }

    protected function hydrateEntity(object $entity, array $payload, bool $isUpdate): void
    {
        if (!$entity instanceof Employee) {
            throw new HttpException(500, 'Unexpected employee entity.');
        }

        if (array_key_exists('store_id', $payload)) {
            $entity->setStore($this->referenceResolver->require(Store::class, (int) $payload['store_id'], 'Store'));
        } elseif (!$isUpdate) {
            throw new HttpException(422, 'The store_id field is required.');
        }

        if (array_key_exists('employee_name', $payload)) {
            $entity->setEmployeeName((string) $payload['employee_name']);
        } elseif (!$isUpdate) {
            throw new HttpException(422, 'The employee_name field is required.');
        }

        if (array_key_exists('employee_email', $payload)) {
            $entity->setEmployeeEmail((string) $payload['employee_email']);
        } elseif (!$isUpdate) {
            throw new HttpException(422, 'The employee_email field is required.');
        }

        if (array_key_exists('employee_password', $payload)) {
            $entity->setEmployeePassword((string) $payload['employee_password']);
        } elseif (!$isUpdate) {
            throw new HttpException(422, 'The employee_password field is required.');
        }

        if (array_key_exists('employee_role', $payload)) {
            $role = (string) $payload['employee_role'];

            if (!in_array($role, ['employee', 'chief', 'it'], true)) {
                throw new HttpException(422, 'The employee_role field must be employee, chief, or it.');
            }

            $entity->setEmployeeRole($role);
        } elseif (!$isUpdate) {
            throw new HttpException(422, 'The employee_role field is required.');
        }
    }

    protected function serializeEntity(object $entity): array
    {
        /** @var Employee $entity */
        return [
            'employee_id' => $entity->getEmployeeId(),
            'store_id' => $entity->getStore()?->getStoreId(),
            'employee_name' => $entity->getEmployeeName(),
            'employee_email' => $entity->getEmployeeEmail(),
            'employee_role' => $entity->getEmployeeRole(),
        ];
    }

    protected function getEntityClass(): string
    {
        return Employee::class;
    }

    protected function getResourceName(): string
    {
        return 'employee';
    }

    protected function createEntityInstance(): object
    {
        return new Employee();
    }

    /**
     * @return array<string, mixed>
     */
    private function requireSessionEmployee(): array
    {
        $employee = $_SESSION['employee'] ?? null;

        if (!is_array($employee) || !isset($employee['employee_role'], $employee['store_id'])) {
            throw new HttpException(401, 'Authentication required.');
        }

        return $employee;
    }
}