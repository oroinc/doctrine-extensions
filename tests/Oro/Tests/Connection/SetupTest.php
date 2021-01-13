<?php
declare(strict_types=1);

namespace Oro\Tests\Connection;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use Oro\Tests\TestCase;

class SetupTest extends TestCase
{
    /**
     * @throws ORMException
     * @throws ToolsException
     */
    public function testSchemaUp(): void
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
        static::assertNotEmpty($tables);

        $this->loadFixtures();
    }

    protected function loadFixtures(): void
    {
        $loader = new Loader();
        $loader->loadFromDirectory(__DIR__ . '/Fixtures');

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }
}
