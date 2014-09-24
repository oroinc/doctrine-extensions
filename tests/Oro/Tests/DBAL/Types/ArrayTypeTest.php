<?php

namespace Oro\Tests\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Oro\Tests\Connection\TestUtil;

class ArrayTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider serializationDataProvider
     * @param array $data
     */
    public function testSerialization($data)
    {
        $encoded = base64_encode(serialize($data));

        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        $type = $this->getType();

        $actualDbValue = $type->convertToDatabaseValue($data, $platform);
        $this->assertEquals($encoded, $actualDbValue);
        $this->assertEquals($data, $type->convertToPHPValue($actualDbValue, $platform));
        $this->assertEquals($data, $type->convertToPHPValue($encoded, $platform));
    }

    /**
     * @dataProvider serializationDataProvider
     * @param array $data
     */
    public function testCompatibilityMode($data)
    {
        $dataSerialized = serialize($data);

        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        $type = $this->getType();

        $this->assertEquals($data, $type->convertToPHPValue($dataSerialized, $platform));
    }

    /**
     * @return Type
     */
    protected function getType()
    {
        Type::overrideType(Type::TARRAY, 'Oro\DBAL\Types\ArrayType');
        return Type::getType(Type::TARRAY);
    }

    /**
     * @return array
     */
    public function serializationDataProvider()
    {
        return array(
            array(array('a' => 'b')),
            array(array()),
            array(array(1, 2, 3)),
        );
    }
}
