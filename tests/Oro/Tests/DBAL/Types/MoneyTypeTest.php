<?php

namespace Oro\Tests\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Oro\DBAL\Types\MoneyType;
use Oro\Tests\Connection\TestUtil;

class MoneyTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MoneyType
     */
    protected $type;

    protected function setUp()
    {
        if (!Type::hasType(MoneyType::TYPE)) {
            Type::addType(MoneyType::TYPE, 'Oro\DBAL\Types\MoneyType');
        }
        $this->type = Type::getType(MoneyType::TYPE);
    }

    public function testGetName()
    {
        $this->assertEquals('money', $this->type->getName());
    }

    public function testGetSQLDeclaration()
    {
        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        $output = $this->type->getSQLDeclaration(array(), $platform);

        $this->assertEquals("NUMERIC(19, 4)", $output);
    }

    public function testConvertToPHPValue()
    {
        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        $this->assertNull($this->type->convertToPHPValue(null, $platform));
        $this->assertEquals(12.1, $this->type->convertToPHPValue(12.1, $platform));
    }

    public function testRequiresSQLCommentHint()
    {
        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        $this->assertTrue($this->type->requiresSQLCommentHint($platform));
    }
}
