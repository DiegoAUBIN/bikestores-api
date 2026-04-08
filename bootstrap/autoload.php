<?php

declare(strict_types=1);

$baseDirectory = dirname(__DIR__);

spl_autoload_register(
    static function (string $className) use ($baseDirectory): void {
        $prefix = 'App\\';

        if (!str_starts_with($className, $prefix)) {
            return;
        }

        $relativeClass = substr($className, strlen($prefix));
        $filePath = $baseDirectory . '/src/' . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($filePath)) {
            require_once $filePath;
        }
    }
);

$vendorAutoload = $baseDirectory . '/vendor/autoload.php';

if (is_file($vendorAutoload)) {
    require_once $vendorAutoload;
}