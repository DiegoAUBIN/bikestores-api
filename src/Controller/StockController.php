<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Stock;
use App\Entity\Store;
use App\Exception\HttpException;
use App\Http\JsonResponse;
use App\Http\Request;

/**
 * Handles stock API requests.
 */
class StockController extends AbstractCrudController
{
    public function store(Request $request): JsonResponse
    {
        $this->assertOwnStoreForWrite($request);

        return parent::store($request);
    }

    public function update(Request $request): JsonResponse
    {
        $this->assertOwnStoreForWrite($request);

        return parent::update($request);
    }

    public function destroy(Request $request): JsonResponse
    {
        $this->assertOwnStoreForWrite($request);

        return parent::destroy($request);
    }

    protected function findEntity(Request $request): ?object
    {
        $storeId = $request->getRouteParam('storeId');
        $productId = $request->getRouteParam('productId');

        if ($storeId === null || $productId === null || !ctype_digit($storeId) || !ctype_digit($productId)) {
            throw new HttpException(400, 'Numeric storeId and productId route parameters are required.');
        }

        return $this->entityManager->find(Stock::class, ['store' => (int) $storeId, 'product' => (int) $productId]);
    }

    protected function hydrateEntity(object $entity, array $payload, bool $isUpdate): void
    {
        if (!$entity instanceof Stock) {
            throw new HttpException(500, 'Unexpected stock entity.');
        }

        if (array_key_exists('store_id', $payload)) {
            $entity->setStore($this->referenceResolver->require(Store::class, (int) $payload['store_id'], 'Store'));
        } elseif (!$isUpdate) {
            throw new HttpException(422, 'The store_id field is required.');
        }

        if (array_key_exists('product_id', $payload)) {
            $entity->setProduct($this->referenceResolver->require(Product::class, (int) $payload['product_id'], 'Product'));
        } elseif (!$isUpdate) {
            throw new HttpException(422, 'The product_id field is required.');
        }

        if (array_key_exists('quantity', $payload)) {
            $entity->setQuantity((int) $payload['quantity']);
        } elseif (!$isUpdate) {
            throw new HttpException(422, 'The quantity field is required.');
        }
    }

    protected function serializeEntity(object $entity): array
    {
        /** @var Stock $entity */
        return [
            'store_id' => $entity->getStore()?->getStoreId(),
            'product_id' => $entity->getProduct()?->getProductId(),
            'quantity' => $entity->getQuantity(),
        ];
    }

    protected function getEntityClass(): string
    {
        return Stock::class;
    }

    protected function getResourceName(): string
    {
        return 'stock';
    }

    protected function createEntityInstance(): object
    {
        return new Stock();
    }

    private function assertOwnStoreForWrite(Request $request): void
    {
        $employee = $_SESSION['employee'] ?? null;

        if (!is_array($employee) || !isset($employee['employee_role'], $employee['store_id'])) {
            throw new HttpException(401, 'Authentication required.');
        }

        if ((string) $employee['employee_role'] === 'it') {
            return;
        }

        $payload = $request->getParsedBody();
        $payloadStore = $payload['store_id'] ?? null;
        $routeStore = $request->getRouteParam('storeId');

        if ($payloadStore !== null && (int) $payloadStore !== (int) $employee['store_id']) {
            throw new HttpException(403, 'You can only manage stock in your own store.');
        }

        if ($routeStore !== null && ctype_digit($routeStore) && (int) $routeStore !== (int) $employee['store_id']) {
            throw new HttpException(403, 'You can only manage stock in your own store.');
        }
    }
}