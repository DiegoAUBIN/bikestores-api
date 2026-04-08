<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Exception\HttpException;

/**
 * Handles category API requests.
 */
class CategoryController extends AbstractCrudController
{
    protected function hydrateEntity(object $entity, array $payload, bool $isUpdate): void
    {
        if (!$entity instanceof Category) {
            throw new HttpException(500, 'Unexpected category entity.');
        }

        if (array_key_exists('category_name', $payload)) {
            $entity->setCategoryName((string) $payload['category_name']);
        } elseif (!$isUpdate) {
            throw new HttpException(422, 'The category_name field is required.');
        }
    }

    protected function serializeEntity(object $entity): array
    {
        /** @var Category $entity */
        return [
            'category_id' => $entity->getCategoryId(),
            'category_name' => $entity->getCategoryName(),
        ];
    }

    protected function getEntityClass(): string
    {
        return Category::class;
    }

    protected function getResourceName(): string
    {
        return 'category';
    }

    protected function createEntityInstance(): object
    {
        return new Category();
    }
}