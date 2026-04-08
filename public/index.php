<?php

declare(strict_types=1);

use App\Bootstrap\DoctrineFactory;
use App\Controller\AuthController;
use App\Controller\BrandController;
use App\Controller\CategoryController;
use App\Controller\EmployeeController;
use App\Controller\ProductController;
use App\Controller\StockController;
use App\Controller\StoreController;
use App\Exception\HttpException;
use App\Http\JsonResponse;
use App\Http\Request;
use App\Http\Router;
use App\Middleware\ApiKeyMiddleware;
use App\Middleware\SessionAuthMiddleware;
use App\Service\EntityReferenceResolver;

require_once dirname(__DIR__) . '/bootstrap/autoload.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$appConfig = require dirname(__DIR__) . '/config/app.php';
$request = Request::fromGlobals($appConfig['base_path']);

if ($request->getPath() === '/') {
    header('Content-Type: text/html; charset=utf-8');

    echo <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BikeStores API</title>
</head>
<body>
    <h1>BikeStores API</h1>
    <p>The REST API is available under the current base URL.</p>
    <ul>
        <li><a href="site">Employee site</a></li>
        <li><a href="swagger">Swagger UI</a></li>
        <li><a href="openapi.yaml">OpenAPI specification</a></li>
        <li><a href="stores">GET /stores</a></li>
        <li><a href="products/10">GET /products/10</a></li>
    </ul>
</body>
</html>
HTML;

    return;
}

try {
    $entityManager = DoctrineFactory::createEntityManager();
    $referenceResolver = new EntityReferenceResolver($entityManager);
    $router = new Router();
    $authController = new AuthController($entityManager);
    $brandController = new BrandController($entityManager, $referenceResolver);
    $categoryController = new CategoryController($entityManager, $referenceResolver);
    $storeController = new StoreController($entityManager, $referenceResolver);
    $productController = new ProductController($entityManager, $referenceResolver);
    $stockController = new StockController($entityManager, $referenceResolver);
    $employeeController = new EmployeeController($entityManager, $referenceResolver);
    $apiKeyMiddleware = new ApiKeyMiddleware($appConfig['api_key']);
    $sessionAuthMiddleware = new SessionAuthMiddleware(['employee', 'chief', 'it']);
    $chiefOrItMiddleware = new SessionAuthMiddleware(['chief', 'it']);

    $registerRoutes = require dirname(__DIR__) . '/routes/api.php';
    $registerRoutes(
        $router,
        $authController,
        $brandController,
        $categoryController,
        $storeController,
        $productController,
        $stockController,
        $employeeController,
        $apiKeyMiddleware,
        $sessionAuthMiddleware,
        $chiefOrItMiddleware
    );

    $response = $router->dispatch($request);
} catch (HttpException $exception) {
    $response = JsonResponse::create(
        [
            'status' => $exception->getStatusCode(),
            'error' => $exception->getMessage(),
            'details' => $exception->getPayload(),
        ],
        $exception->getStatusCode()
    );
} catch (Throwable $throwable) {
    $statusCode = 500;
    $payload = [
        'status' => $statusCode,
        'error' => 'Internal Server Error',
    ];

    if ((bool) $appConfig['debug']) {
        $payload['message'] = $throwable->getMessage();
    }

    $response = JsonResponse::create($payload, $statusCode);
}

$response->send();