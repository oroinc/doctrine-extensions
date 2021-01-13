<?php
declare(strict_types=1);

namespace Oro\Tests\DBAL\Types;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\ORMException;
use Oro\Tests\Connection\TestUtil;
use PHPUnit\Framework\TestCase;

class ArrayTypeTest extends TestCase
{
    /**
     * @dataProvider serializationDataProvider
     * @throws Exception
     * @throws ORMException
     */
    public function testSerialization(array $data): void
    {
        $encoded = \base64_encode(\serialize($data));

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
    public function testCompatibilityMode(array $data): void
    {
        $dataSerialized = \serialize($data);

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
        Type::overrideType(Types::ARRAY, \Oro\DBAL\Types\ArrayType::class);
        return Type::getType(Types::ARRAY);
    }

    public function serializationDataProvider(): array
    {
        return [
            [['a' => 'b']],
            [[]],
            [[1, 2, 3]],
        ];
    }
}
