<?php

namespace Oro\Tests\Connection;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class TestUtil
{
    /**
     * @var EntityManager
     */
    private static $entityManager;

    /**
     * Get entity manager instance.
     *
     * @return EntityManager
     *
     * @throws \Exception
     */
    public static function getEntityManager()
    {
        if (!self::$entityManager && self::hasRequiredConnectionParams()) {
            $dbParams = self::getConnectionParams();
            $entitiesPath = realpath(__DIR__ . '/../../Entities');

            $config = Setup::createAnnotationMetadataConfiguration(array($entitiesPath), true);
            self::$entityManager = EntityManager::create($dbParams, $config);
        }

        if (self::$entityManager) {
            return self::$entityManager;
        } else {
            throw new \Exception('Database connection not configured');
        }
    }

    private static function hasRequiredConnectionParams()
    {
        if (!isset($GLOBALS['db_type'])) {
            return false;
        }

        switch ($GLOBALS['db_type']) {
            case 'pdo_sqlite':
                $requires = array(
                    'db_path'
                );
                break;
            default:
                $requires = array(
                    'db_type',
                    'db_username',
                    'db_password',
                    'db_host',
                    'db_name',
                    'db_port',
                );
                break;
        }

        foreach ($requires as $requiredParam) {
            if (!isset($GLOBALS[$requiredParam])) {
                return false;
            }
        }

        return true;
    }

    private static function getConnectionParams()
    {
        switch ($GLOBALS['db_type']) {
            case 'pdo_sqlite':
                $connectionParams = array(
                    'driver' => $GLOBALS['db_type'],
                    'path' => $GLOBALS['db_path']
                );
                break;
            default:
                $connectionParams = array(
                    'driver' => $GLOBALS['db_type'],
                    'user' => $GLOBALS['db_username'],
                    'password' => $GLOBALS['db_password'],
                    'host' => $GLOBALS['db_host'],
                    'dbname' => $GLOBALS['db_name'],
                    'port' => $GLOBALS['db_port']
                );
                break;
        }

        if (isset($GLOBALS['db_server'])) {
            $connectionParams['server'] = $GLOBALS['db_server'];
        }

        if (isset($GLOBALS['db_unix_socket'])) {
            $connectionParams['unix_socket'] = $GLOBALS['db_unix_socket'];
        }

        return $connectionParams;
    }

    /**
     * Get database platform name.
     *
     * @return string
     *
     * @throws \Exception
     */
    public static function getPlatformName()
    {
        $entityManager = self::getEntityManager();

        return $entityManager->getConnection()->getDatabasePlatform()->getName();
    }
}
