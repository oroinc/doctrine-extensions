<?php
declare(strict_types=1);

namespace Oro\Tests\DBAL\Types;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Type;

use Doctrine\ORM\ORMException;
use Oro\DBAL\Types\PercentType;
use Oro\Tests\Connection\TestUtil;
use PHPUnit\Framework\TestCase;

class PercentTypeTest extends TestCase
{
    /** @var PercentType */
    protected $percentType;

    /**
     * @throws Exception
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function setUp(): void
    {
        if (!Type::hasType(PercentType::TYPE)) {
            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
            Type::addType(PercentType::TYPE, \Oro\DBAL\Types\PercentType::class);
        }
        $this->percentType = Type::getType(PercentType::TYPE);
    }

    public function testGetName(): void
    {
        static::assertEquals('percent', $this->percentType->getName());
    }

    /**
     * @throws Exception
     * @throws ORMException
     */
    public function testGetSQLDeclaration(): void
    {
        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        $output = $this->percentType->getSQLDeclaration([], $platform);

        static::assertEquals("DOUBLE PRECISION", $output);
    }

    /**
     * @throws Exception
     * @throws ORMException
     */
    public function testConvertToPHPValue(): void
    {
        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        static::assertNull($this->percentType->convertToPHPValue(null, $platform));
        static::assertEquals(12.4, $this->percentType->convertToPHPValue(12.4, $platform));
    }

    /**
     * @throws Exception
     * @throws ORMException
     */
    public function testRequiresSQLCommentHint(): void
    {
        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        static::assertTrue($this->percentType->requiresSQLCommentHint($platform));
    }
}
