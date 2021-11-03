<?php

namespace Oro\Tests;

use Doctrine\ORM\EntityManager;
use Oro\Tests\Connection\TestUtil;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $metadata;

    protected function setUp(): void
    {
        $this->entityManager = TestUtil::getEntityManager();
        $this->metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
    }

    protected function tearDown(): void
    {
        unset($this->entityManager);
    }
}
