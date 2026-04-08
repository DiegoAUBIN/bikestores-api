<?php

declare(strict_types=1);

namespace App\Bootstrap;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use RuntimeException;

use function dirname;

/**
 * Builds the Doctrine entity manager.
 */
final class DoctrineFactory
{
    /**
     * Creates the Doctrine entity manager instance.
     */
    public static function createEntityManager(): EntityManager
    {
        if (!class_exists(EntityManager::class) || !class_exists(ORMSetup::class)) {
            throw new RuntimeException('Doctrine is not installed. Run composer install before starting the API.');
        }

        $appConfig = require dirname(__DIR__, 2) . '/config/app.php';
        $databaseConfig = require dirname(__DIR__, 2) . '/config/database.php';

        $doctrineConfig = ORMSetup::createAttributeMetadataConfiguration(
            paths: [dirname(__DIR__) . '/Entity'],
            isDevMode: (bool) $appConfig['debug'],
        );

        return EntityManager::create($databaseConfig, $doctrineConfig);
    }
}