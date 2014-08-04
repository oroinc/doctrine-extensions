<?php

namespace Oro\Tests\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Oro\Tests\Connection\TestUtil;

class ArrayTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialization()
    {
        $array = array('a' => 'b');
        $encoded = base64_encode(serialize($array));

        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        Type::overrideType(Type::TARRAY, 'Oro\DBAL\Types\ArrayType');
        $type = Type::getType(Type::TARRAY);

        $actualDbValue = $type->convertToDatabaseValue($array, $platform);
        $this->assertEquals($encoded, $actualDbValue);
        $this->assertEquals($array, $type->convertToPHPValue($actualDbValue, $platform));
        $this->assertEquals($array, $type->convertToPHPValue($encoded, $platform));
    }
}
