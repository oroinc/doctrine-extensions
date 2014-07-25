<?php

namespace Oro\Tests;

use Doctrine\ORM\EntityManager;
use Oro\Tests\Connection\TestUtil;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var array
     */
    protected $metadata;

    protected function setUp()
    {
        $this->entityManager = TestUtil::getEntityManager();
        $this->metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
    }

    protected function tearDown()
    {
        unset($this->entityManager);
    }
}
