<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Product;
use App\Exception\HttpException;
use App\Http\JsonResponse;
use App\Http\Request;

/**
 * Handles product API requests.
 */
class ProductController extends AbstractCrudController
{
    /**
     * Updates one product.
     *
     * Non-IT users can only update products available in their own store stock.
     */
    public function update(Request $request): JsonResponse
    {
        $this->assertProductInOwnStoreForNonIt($request);

        return parent::update($request);
    }

    /**
     * Deletes one product.
     *
     * Non-IT users can only delete products available in their own store stock.
     */
    public function destroy(Request $request): JsonResponse
    {
        $this->assertProductInOwnStoreForNonIt($request);

        return parent::destroy($request);
    }

    protected function hydrateEntity(object $entity, array $payload, bool $isUpdate): void
    {
        if (!$entity instanceof Product) {
            throw new HttpException(500, 'Unexpected product entity.');
        }

        if (array_key_exists('product_name', $payload)) {
            $entity->setProductName((string) $payload['product_name']);
        } elseif (!$isUpdate) {
            throw new HttpException(422, 'The product_name field is required.');
        }

        if (array_key_exists('brand_id', $payload)) {
            $entity->setBrand($this->referenceResolver->require(Brand::class, (int) $payload['brand_id'], 'Brand'));
        } elseif (!$isUpdate) {
            throw new HttpException(422, 'The brand_id field is required.');
        }

        if (array_key_exists('category_id', $payload)) {
            $entity->setCategory($this->referenceResolver->require(Category::class, (int) $payload['category_id'], 'Category'));
        } elseif (!$isUpdate) {
            throw new HttpException(422, 'The category_id field is required.');
        }

        if (array_key_exists('model_year', $payload)) {
            $entity->setModelYear((int) $payload['model_year']);
        } elseif (!$isUpdate) {
            throw new HttpException(422, 'The model_year field is required.');
        }

        if (array_key_exists('list_price', $payload)) {
            $entity->setListPrice((string) $payload['list_price']);
        } elseif (!$isUpdate) {
            throw new HttpException(422, 'The list_price field is required.');
        }
    }

    protected function serializeEntity(object $entity): array
    {
        /** @var Product $entity */
        return [
            'product_id' => $entity->getProductId(),
            'product_name' => $entity->getProductName(),
            'brand_id' => $entity->getBrand()?->getBrandId(),
            'category_id' => $entity->getCategory()?->getCategoryId(),
            'model_year' => $entity->getModelYear(),
            'list_price' => $entity->getListPrice(),
        ];
    }

    protected function getEntityClass(): string
    {
        return Product::class;
    }

    protected function getResourceName(): string
    {
        return 'product';
    }

    protected function createEntityInstance(): object
    {
        return new Product();
    }

    /**
     * Checks that non-IT users can only manage products linked to their store via stock.
     */
    private function assertProductInOwnStoreForNonIt(Request $request): void
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

        /** @var Product|null $product */
        $product = $this->entityManager->find(Product::class, (int) $routeId);

        if ($product === null) {
            throw new HttpException(404, 'Product not found.');
        }

        $ownStoreId = (int) $employee['store_id'];
        $allowed = false;

        foreach ($product->getStocks() as $stock) {
            if ((int) ($stock->getStore()?->getStoreId() ?? 0) === $ownStoreId) {
                $allowed = true;
                break;
            }
        }

        if (!$allowed) {
            throw new HttpException(403, 'You can only manage products available in your own store.');
        }
    }
}