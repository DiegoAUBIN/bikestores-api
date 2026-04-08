<?php

declare(strict_types=1);

return [
    'app_name' => 'BikeStores API',
    'base_path' => $_ENV['APP_BASE_PATH'] ?? getenv('APP_BASE_PATH') ?: '/bikestores',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? getenv('APP_DEBUG') ?: '1', FILTER_VALIDATE_BOOL),
    'api_key' => $_ENV['API_ACCESS_KEY'] ?? getenv('API_ACCESS_KEY') ?: 'e8f1997c763',
];