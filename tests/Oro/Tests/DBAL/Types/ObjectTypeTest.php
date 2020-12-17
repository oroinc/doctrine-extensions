<?php
declare(strict_types=1);

namespace Oro\Tests\DBAL\Types;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\ORMException;
use Oro\Tests\Connection\TestUtil;
use PHPUnit\Framework\TestCase;

class ObjectTypeTest extends TestCase
{
    /**
     * @dataProvider serializationDataProvider
     * @throws Exception
     * @throws ORMException
     */
    public function testSerialization(object $data): void
    {
        $encoded = base64_encode(serialize($data));

        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        $type = $this->getType();

        $actualDbValue = $type->convertToDatabaseValue($data, $platform);
        static::assertEquals($encoded, $actualDbValue);
        static::assertEquals($data, $type->convertToPHPValue($actualDbValue, $platform));
        static::assertEquals($data, $type->convertToPHPValue($encoded, $platform));
    }

    /**
     * @dataProvider serializationDataProvider
     * @throws Exception
     * @throws ORMException
     */
    public function testCompatibilityMode(object $data): void
    {
        $dataSerialized = serialize($data);

        $platform = TestUtil::getEntityManager()->getConnection()->getDatabasePlatform();
        $type = $this->getType();

        static::assertEquals($data, $type->convertToPHPValue($dataSerialized, $platform));
    }

    /**
     * @throws Exception
     */
    protected function getType(): Type
    {
        /** @noinspection PhpFullyQualifiedNameUsageInspection */
        Type::overrideType(Types::OBJECT, \Oro\DBAL\Types\ObjectType::class);
        return Type::getType(Types::OBJECT);
    }

    public function serializationDataProvider(): array
    {
        $object = new \stdClass();
        $object->a = 'test1';

        $emptyObject = new \stdClass();

        return [
            [$object],
            [$emptyObject]
        ];
    }
}
