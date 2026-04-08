<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Store;
use App\Exception\HttpException;
use App\Http\JsonResponse;
use App\Http\Request;

/**
 * Handles store API requests.
 */
class StoreController extends AbstractCrudController
{
    /**
     * Creates a store entry.
     *
     * Non-IT users are allowed by specification through shared write permissions.
     */
    public function store(Request $request): JsonResponse
    {
        return parent::store($request);
    }

    /**
     * Updates a store.
     *
     * Chief/employee users can only update their own store.
     */
    public function update(Request $request): JsonResponse
    {
        $this->assertOwnStoreForNonIt($request);

        return parent::update($request);
    }

    /**
     * Deletes a store.
     *
     * Chief/employee users can only delete their own store.
     */
    public function destroy(Request $request): JsonResponse
    {
        $this->assertOwnStoreForNonIt($request);

        return parent::destroy($request);
    }

    protected function hydrateEntity(object $entity, array $payload, bool $isUpdate): void
    {
        if (!$entity instanceof Store) {
            throw new HttpException(500, 'Unexpected store entity.');
        }

        if (array_key_exists('store_name', $payload)) {
            $entity->setStoreName((string) $payload['store_name']);
        } elseif (!$isUpdate) {
            throw new HttpException(422, 'The store_name field is required.');
        }

        if (array_key_exists('phone', $payload)) {
            $entity->setPhone($payload['phone'] !== null ? (string) $payload['phone'] : null);
        }

        if (array_key_exists('email', $payload)) {
            $entity->setEmail($payload['email'] !== null ? (string) $payload['email'] : null);
        }

        if (array_key_exists('street', $payload)) {
            $entity->setStreet($payload['street'] !== null ? (string) $payload['street'] : null);
        }

        if (array_key_exists('city', $payload)) {
            $entity->setCity($payload['city'] !== null ? (string) $payload['city'] : null);
        }

        if (array_key_exists('state', $payload)) {
            $entity->setState($payload['state'] !== null ? (string) $payload['state'] : null);
        }

        if (array_key_exists('zip_code', $payload)) {
            $entity->setZipCode($payload['zip_code'] !== null ? (string) $payload['zip_code'] : null);
        }
    }

    protected function serializeEntity(object $entity): array
    {
        /** @var Store $entity */
        return [
            'store_id' => $entity->getStoreId(),
            'store_name' => $entity->getStoreName(),
            'phone' => $entity->getPhone(),
            'email' => $entity->getEmail(),
            'street' => $entity->getStreet(),
            'city' => $entity->getCity(),
            'state' => $entity->getState(),
            'zip_code' => $entity->getZipCode(),
        ];
    }

    protected function getEntityClass(): string
    {
        return Store::class;
    }

    protected function getResourceName(): string
    {
        return 'store';
    }

    protected function createEntityInstance(): object
    {
        return new Store();
    }

    /**
     * Checks store-scoped permissions for non-IT users.
     */
    private function assertOwnStoreForNonIt(Request $request): void
    {
        $employee = $_SESSION['employee'] ?? null;

        if (!is_array($employee) || !isset($employee['employee_role'], $employee['store_id'])) {
            throw new HttpException(401, 'Authentication required.');
        }

        if ((string) $employee['employee_role'] === 'it') {
            return;
        }

        $routeId = $request->getRouteParam('id');

        if ($routeId === null || !ctype_digit($routeId)) {
            throw new HttpException(400, 'A numeric identifier is required.');
        }

        if ((int) $routeId !== (int) $employee['store_id']) {
            throw new HttpException(403, 'You can only manage your own store.');
        }
    }
}