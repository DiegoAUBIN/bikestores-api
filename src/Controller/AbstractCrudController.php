<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\HttpException;
use App\Http\JsonResponse;
use App\Http\Request;
use App\Service\EntityReferenceResolver;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

/**
 * Provides shared CRUD actions for API resources.
 */
abstract class AbstractCrudController
{
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        protected readonly EntityReferenceResolver $referenceResolver
    ) {
    }

    /**
     * Returns all resource items.
     */
    public function index(Request $request): JsonResponse
    {
        $items = array_map(fn (object $entity): array => $this->serializeEntity($entity), $this->repository()->findAll());

        return JsonResponse::create([
            'resource' => $this->getResourceName(),
            'count' => count($items),
            'items' => $items,
        ]);
    }

    /**
     * Returns one resource item.
     */
    public function show(Request $request): JsonResponse
    {
        $entity = $this->requireEntity($request);

        return JsonResponse::create($this->serializeEntity($entity));
    }

    /**
     * Creates one resource item.
     */
    public function store(Request $request): JsonResponse
    {
        $payload = $this->requirePayload($request);
        $entity = $this->createEntityInstance();

        $this->hydrateEntity($entity, $payload, false);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return JsonResponse::create($this->serializeEntity($entity), 201);
    }

    /**
     * Updates one resource item.
     */
    public function update(Request $request): JsonResponse
    {
        $entity = $this->requireEntity($request);
        $payload = $this->requirePayload($request);

        $this->hydrateEntity($entity, $payload, true);
        $this->entityManager->flush();

        return JsonResponse::create($this->serializeEntity($entity));
    }

    /**
     * Deletes one resource item.
     */
    public function destroy(Request $request): JsonResponse
    {
        $entity = $this->requireEntity($request);

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return JsonResponse::create([
            'message' => sprintf('%s deleted successfully.', ucfirst($this->getResourceName())),
        ]);
    }

    /**
     * Returns the repository instance.
     */
    protected function repository(): ObjectRepository
    {
        return $this->entityManager->getRepository($this->getEntityClass());
    }

    /**
     * Returns the resource item for the current route.
     */
    protected function requireEntity(Request $request): object
    {
        $entity = $this->findEntity($request);

        if ($entity === null) {
            throw new HttpException(404, sprintf('%s not found.', ucfirst($this->getResourceName())));
        }

        return $entity;
    }

    /**
     * Returns the resource item for the current route or null.
     */
    protected function findEntity(Request $request): ?object
    {
        $identifier = $request->getRouteParam('id');

        if ($identifier === null || !ctype_digit($identifier)) {
            throw new HttpException(400, 'A numeric identifier is required.');
        }

        return $this->entityManager->find($this->getEntityClass(), (int) $identifier);
    }

    /**
     * Returns the JSON payload.
     *
     * @return array<string, mixed>
     */
    protected function requirePayload(Request $request): array
    {
        $payload = $request->getParsedBody();

        if ($payload === []) {
            throw new HttpException(400, 'A JSON payload is required.');
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $payload
     */
    abstract protected function hydrateEntity(object $entity, array $payload, bool $isUpdate): void;

    /**
     * @return array<string, mixed>
     */
    abstract protected function serializeEntity(object $entity): array;

    /**
     * @return class-string<object>
     */
    abstract protected function getEntityClass(): string;

    abstract protected function getResourceName(): string;

    abstract protected function createEntityInstance(): object;
}