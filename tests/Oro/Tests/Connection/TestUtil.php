<?php
declare(strict_types=1);

namespace Oro\Tests\Connection;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class TestUtil
{
    /** @var EntityManager */
    private static $entityManager;

    /**
     * @throws \RuntimeException
     * @throws \Doctrine\ORM\ORMException
     */
    public static function getEntityManager(): EntityManager
    {
        if (!self::$entityManager && self::hasRequiredConnectionParams()) {
            $dbParams = self::getConnectionParams();
            $entitiesPath = \realpath(__DIR__ . '/../../Entities');

            $config = Setup::createAnnotationMetadataConfiguration([$entitiesPath], true);
            self::$entityManager = EntityManager::create($dbParams, $config);
        }

        if (self::$entityManager) {
            return self::$entityManager;
        }

        throw new \RuntimeException('Database connection not configured');
    }

    private static function hasRequiredConnectionParams(): bool
    {
        return isset(
            $GLOBALS['db_type'],
            $GLOBALS['db_username'],
            $GLOBALS['db_password'],
            $GLOBALS['db_host'],
            $GLOBALS['db_name'],
            $GLOBALS['db_port']
        );
    }

    private static function getConnectionParams(): array
    {
        $connectionParams = [
            'driver' => $GLOBALS['db_type'],
            'user' => $GLOBALS['db_username'],
            'password' => $GLOBALS['db_password'],
            'host' => $GLOBALS['db_host'],
            'dbname' => $GLOBALS['db_name'],
            'port' => $GLOBALS['db_port']
        ];

        if (isset($GLOBALS['db_server'])) {
            $connectionParams['server'] = $GLOBALS['db_server'];
        }

        if (isset($GLOBALS['db_unix_socket'])) {
            $connectionParams['unix_socket'] = $GLOBALS['db_unix_socket'];
        }

        return $connectionParams;
    }

    /**
     * @throws \Exception
     */
    public static function getPlatformName(): string
    {
        $entityManager = self::getEntityManager();
        return $entityManager->getConnection()->getDatabasePlatform()->getName();
    }
}
