<?php
declare(strict_types=1);

namespace Oro\Tests\ORM\AST;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\OraclePlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\Query\QueryException;
use Oro\ORM\Query\AST\FunctionFactory;
use PHPUnit\Framework\TestCase;

class FunctionFactoryTest extends TestCase
{
    public function testCreateExceptionNotSupportedPlatform(): void
    {
        $platform = $this->createMock(OraclePlatform::class);
        $this->expectException(QueryException::class);
        $this->expectExceptionMessage(sprintf('[Syntax Error] Not supported platform "%s"', $platform::class));
        FunctionFactory::create($platform, 'date', []);
    }

    public function testCreateExceptionNotSupportedFunction(): void
    {
        $platform = $this->createMock(PostgreSQLPlatform::class);
        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('[Syntax Error] Function "testF" does not supported for platform "postgresql"');
        FunctionFactory::create($platform, 'testF', []);
    }

    /**
     * @dataProvider platformFunctionsDataProvider
     * @throws QueryException
     */
    public function testCreate(string $platform, string $function): void
    {
        $this->expectNotToPerformAssertions();
        FunctionFactory::create($this->createMock($platform), $function, []);
    }

    public static function platformFunctionsDataProvider(): array
    {
        return [
            [MySQLPlatform::class, 'date'],
            [MySQLPlatform::class, 'Date'],
            [MySQLPlatform::class, 'DATE'],
            [PostgreSQLPlatform::class, 'group_concat'],
            [MySQLPlatform::class, 'Group_Concat'],
            [PostgreSQLPlatform::class, 'GROUP_CONCAT'],
            [MySQLPlatform::class, 'TimestampDiff'],
            [PostgreSQLPlatform::class, 'TIMESTAMPDIFF'],
        ];
    }
}
