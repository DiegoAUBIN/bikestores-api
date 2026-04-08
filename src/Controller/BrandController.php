<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Brand;
use App\Exception\HttpException;

/**
 * Handles brand API requests.
 */
class BrandController extends AbstractCrudController
{
    protected function hydrateEntity(object $entity, array $payload, bool $isUpdate): void
    {
        if (!$entity instanceof Brand) {
            throw new HttpException(500, 'Unexpected brand entity.');
        }

        if (array_key_exists('brand_name', $payload)) {
            $entity->setBrandName((string) $payload['brand_name']);
        } elseif (!$isUpdate) {
            throw new HttpException(422, 'The brand_name field is required.');
        }
    }

    protected function serializeEntity(object $entity): array
    {
        /** @var Brand $entity */
        return [
            'brand_id' => $entity->getBrandId(),
            'brand_name' => $entity->getBrandName(),
        ];
    }

    protected function getEntityClass(): string
    {
        return Brand::class;
    }

    protected function getResourceName(): string
    {
        return 'brand';
    }

    protected function createEntityInstance(): object
    {
        return new Brand();
    }
}