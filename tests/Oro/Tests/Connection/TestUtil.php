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
        if (!self::$entityManager) {
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

    private static function getConnectionParams(): array
    {
        return [
            'driver' => getenv('ORO_DB_DRIVER'),
            'user' => getenv('ORO_DB_USER'),
            'password' => getenv('ORO_DB_PASS'),
            'host' => getenv('ORO_DB_HOST'),
            'dbname' => getenv('ORO_DB_NAME'),
            'port' => getenv('ORO_DB_PORT')
        ];
    }

    /**
     * @throws \Exception
     */
    public static function getPlatformName(): string
    {
        $map = ['pdo_pgsql' => 'postgresql', 'pdo_mysql' => 'mysql'];
        if (empty($map[getenv('ORO_DB_DRIVER')])) {
            throw new \InvalidArgumentException('Configure ORO_DB_DRIVER environment variable, please.');
        }

        return $map[getenv('ORO_DB_DRIVER')];
    }
}
