<?php

namespace Oro\Tests\Connection;

use Doctrine\ORM\Tools\SchemaTool;
use Oro\Tests\TestCase;

class TearDownTest extends TestCase
{
    public function testSchemaDown()
    {
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropDatabase();

        $schemaManager = $this->entityManager->getConnection()->getSchemaManager();
        $tables = $schemaManager->listTableNames();
        $this->assertEmpty($tables);
    }
}
