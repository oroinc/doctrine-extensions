<?php
declare(strict_types=1);

namespace Oro\Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Oro\Tests\Connection\TestUtil;

class TestCase extends \PHPUnit\Framework\TestCase
{
    /** @var EntityManager */
    protected $entityManager;

    /** @var array */
    protected $metadata;

    /**
     * @throws ORMException
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function setUp(): void
    {
        $this->entityManager = TestUtil::getEntityManager();
        $this->metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    protected function tearDown(): void
    {
        unset($this->entityManager);
    }
}
