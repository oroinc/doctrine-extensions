<?php
declare(strict_types=1);

namespace Oro\Tests\Connection;

use Doctrine\ORM\Tools\SchemaTool;
use Oro\Tests\TestCase;

class TearDownTest extends TestCase
{
    public function testSchemaDown(): void
    {
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropDatabase();

        $schemaManager = $this->entityManager->getConnection()->getSchemaManager();
        $tables = $schemaManager->listTableNames();
        static::assertEmpty($tables);
    }
}
