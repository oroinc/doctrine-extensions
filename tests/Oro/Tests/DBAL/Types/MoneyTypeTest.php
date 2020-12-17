<?php
declare(strict_types=1);

namespace Oro\Tests\DBAL\Types;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\ORMException;
use Oro\DBAL\Types\MoneyType;
use Oro\Tests\Connection\TestUtil;
use PHPUnit\Framework\TestCase;

class MoneyTypeTest extends TestCase
{
    /** @var MoneyType */
    protected $type;

    /**
     * @throws Exception
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function setUp(): void
    {
        if (!Type::hasType(MoneyType::TYPE)) {
            /** @noinspection PhpUnnecessaryFullyQualifiedNameInspection */
            Type::addType(MoneyType::TYPE, \Oro\DBAL\Types\MoneyType::class);
        }
        $this->type = Type::getType(MoneyType::TYPE);
    }

    public function testGetName(): void
    {
        static::assertEquals('money', $this->type->getName());
    }

    /**
     * @throws Exception
     * @throws ORMException
     */
    public function testGetSQLDeclaration(): void
    {
        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        $output = $this->type->getSQLDeclaration([], $platform);

        static::assertEquals("NUMERIC(19, 4)", $output);
    }

    /**
     * @throws Exception
     * @throws ORMException
     */
    public function testConvertToPHPValue(): void
    {
        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        static::assertNull($this->type->convertToPHPValue(null, $platform));
        static::assertEquals(12.1, $this->type->convertToPHPValue(12.1, $platform));
    }

    /**
     * @throws Exception
     * @throws ORMException
     */
    public function testRequiresSQLCommentHint(): void
    {
        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        static::assertTrue($this->type->requiresSQLCommentHint($platform));
    }
}
