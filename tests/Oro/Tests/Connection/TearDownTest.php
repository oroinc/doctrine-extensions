<?php

namespace Oro\Tests\Connection;

use Oro\Tests\TestCase;

class TearDownTest extends TestCase
{
    public function testDropDatabase()
    {
        $connection    = $this->entityManager->getConnection();
        $schemaManager = $connection->getSchemaManager();
        $params        = $connection->getParams();

        if (isset($params['master'])) {
            $params = $params['master'];
        }

        $name = isset($params['path']) ? $params['path'] : (isset($params['dbname']) ? $params['dbname'] : false);

        if (!isset($params['path'])) {
            $name = $connection->getDatabasePlatform()->quoteSingleIdentifier($name);
        }

        $schemaManager->dropDatabase($name);

        if ($params['path']) {
            $this->assertFileNotExists($name);
        } else {
            $this->assertNotContains($name, $schemaManager->listDatabases());
        }
    }
}
