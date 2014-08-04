<?php

namespace Oro\Tests\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Oro\Tests\Connection\TestUtil;

class ObjectTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialization()
    {
        $object = new \stdClass();
        $object->a = 'test1';

        $encoded = base64_encode(serialize($object));

        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        Type::overrideType(Type::OBJECT, 'Oro\DBAL\Types\ObjectType');
        $type = Type::getType(Type::OBJECT);

        $actualDbValue = $type->convertToDatabaseValue($object, $platform);
        $this->assertEquals($encoded, $actualDbValue);
        $this->assertEquals($object, $type->convertToPHPValue($actualDbValue, $platform));
        $this->assertEquals($object, $type->convertToPHPValue($encoded, $platform));
    }
}
