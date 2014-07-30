<?php

namespace Oro\Tests\DBAL\Types;

use Doctrine\DBAL\Types\Type;

use Oro\DBAL\Types\PercentType;
use Oro\Tests\Connection\TestUtil;

class PercentTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PercentType
     */
    protected $percentType;

    protected function setUp()
    {
        if (!Type::hasType(PercentType::TYPE)) {
            Type::addType(PercentType::TYPE, 'Oro\DBAL\Types\PercentType');
        }
        $this->percentType = Type::getType(PercentType::TYPE);
    }

    public function testGetName()
    {
        $this->assertEquals('percent', $this->percentType->getName());
    }

    public function testGetSQLDeclaration()
    {
        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        $output = $this->percentType->getSQLDeclaration(array(), $platform);

        $this->assertEquals("DOUBLE PRECISION", $output);
    }

    public function testConvertToPHPValue()
    {
        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        $this->assertNull($this->percentType->convertToPHPValue(null, $platform));
        $this->assertEquals(12.4, $this->percentType->convertToPHPValue(12.4, $platform));
    }

    public function testRequiresSQLCommentHint()
    {
        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        $this->assertTrue($this->percentType->requiresSQLCommentHint($platform));
    }
}
