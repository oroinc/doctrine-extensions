<?php

namespace Oro\Tests\Connection;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Tools\SchemaTool;
use Oro\Tests\TestCase;

class SetupTest extends TestCase
{
    public function testSchemaUp()
    {
        $this->entityManager = TestUtil::getEntityManager();
        $schemaManager = $this->entityManager->getConnection()->getSchemaManager();

        $schemaTool = new SchemaTool($this->entityManager);
        $tables = $schemaManager->listTableNames();
        if (!empty($tables)) {
            $schemaTool->dropSchema($this->metadata);
        }
        $schemaTool->createSchema($this->metadata);

        $tables = $schemaManager->listTableNames();
        $this->assertNotEmpty($tables);

        $this->loadFixtures();
    }

    protected function loadFixtures()
    {
        $loader = new Loader();
        $loader->loadFromDirectory(__DIR__ . '/Fixtures');

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }
}
