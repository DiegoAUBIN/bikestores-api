<?php

declare(strict_types=1);

use App\Controller\BrandController;
use App\Controller\CategoryController;
use App\Controller\EmployeeController;
use App\Controller\ProductController;
use App\Controller\StockController;
use App\Controller\StoreController;
use App\Controller\AuthController;
use App\Http\Router;
use App\Middleware\ApiKeyMiddleware;
use App\Middleware\SessionAuthMiddleware;

return static function (
    Router $router,
    AuthController $authController,
    BrandController $brandController,
    CategoryController $categoryController,
    StoreController $storeController,
    ProductController $productController,
    StockController $stockController,
    EmployeeController $employeeController,
    ApiKeyMiddleware $apiKeyMiddleware,
    SessionAuthMiddleware $sessionAuthMiddleware,
    SessionAuthMiddleware $chiefOrItMiddleware
): void {
    $writeProtected = [$sessionAuthMiddleware, $apiKeyMiddleware];
    $chiefOrItProtected = [$chiefOrItMiddleware];
    $chiefOrItWriteProtected = [$chiefOrItMiddleware, $apiKeyMiddleware];

    $router->add('POST', '/auth/login', [$authController, 'login']);
    $router->add('GET', '/auth/me', [$authController, 'me'], [$sessionAuthMiddleware]);
    $router->add('PUT', '/auth/profile', [$authController, 'updateProfile'], [$sessionAuthMiddleware, $apiKeyMiddleware]);
    $router->add('POST', '/auth/logout', [$authController, 'logout'], [$sessionAuthMiddleware]);

    $router->add('GET', '/brands', [$brandController, 'index']);
    $router->add('GET', '/brands/{id}', [$brandController, 'show']);
    $router->add('POST', '/brands', [$brandController, 'store'], $writeProtected);
    $router->add('PUT', '/brands/{id}', [$brandController, 'update'], $writeProtected);
    $router->add('DELETE', '/brands/{id}', [$brandController, 'destroy'], $writeProtected);

    $router->add('GET', '/categories', [$categoryController, 'index']);
    $router->add('GET', '/categories/{id}', [$categoryController, 'show']);
    $router->add('POST', '/categories', [$categoryController, 'store'], $writeProtected);
    $router->add('PUT', '/categories/{id}', [$categoryController, 'update'], $writeProtected);
    $router->add('DELETE', '/categories/{id}', [$categoryController, 'destroy'], $writeProtected);

    $router->add('GET', '/stores', [$storeController, 'index']);
    $router->add('GET', '/stores/{id}', [$storeController, 'show']);
    $router->add('POST', '/stores', [$storeController, 'store'], $writeProtected);
    $router->add('PUT', '/stores/{id}', [$storeController, 'update'], $writeProtected);
    $router->add('DELETE', '/stores/{id}', [$storeController, 'destroy'], $writeProtected);

    $router->add('GET', '/products', [$productController, 'index']);
    $router->add('GET', '/products/{id}', [$productController, 'show']);
    $router->add('POST', '/products', [$productController, 'store'], $writeProtected);
    $router->add('PUT', '/products/{id}', [$productController, 'update'], $writeProtected);
    $router->add('DELETE', '/products/{id}', [$productController, 'destroy'], $writeProtected);

    $router->add('GET', '/stocks', [$stockController, 'index']);
    $router->add('GET', '/stocks/{storeId}/{productId}', [$stockController, 'show']);
    $router->add('POST', '/stocks', [$stockController, 'store'], $writeProtected);
    $router->add('PUT', '/stocks/{storeId}/{productId}', [$stockController, 'update'], $writeProtected);
    $router->add('DELETE', '/stocks/{storeId}/{productId}', [$stockController, 'destroy'], $writeProtected);

    $router->add('GET', '/employees', [$employeeController, 'index'], $chiefOrItProtected);
    $router->add('GET', '/employees/{id}', [$employeeController, 'show'], $chiefOrItProtected);
    $router->add('POST', '/employees', [$employeeController, 'store'], $chiefOrItWriteProtected);
    $router->add('PUT', '/employees/{id}', [$employeeController, 'update'], $chiefOrItWriteProtected);
    $router->add('DELETE', '/employees/{id}', [$employeeController, 'destroy'], $chiefOrItWriteProtected);
};