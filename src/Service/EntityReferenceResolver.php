<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\HttpException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Resolves related entities from scalar identifiers.
 */
class EntityReferenceResolver
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $entityClass
     *
     * @return T
     */
    public function require(string $entityClass, int $identifier, string $resourceName): object
    {
        $entity = $this->entityManager->find($entityClass, $identifier);

        if ($entity === null) {
            throw new HttpException(404, sprintf('%s with id %d was not found.', $resourceName, $identifier));
        }

        return $entity;
    }
}