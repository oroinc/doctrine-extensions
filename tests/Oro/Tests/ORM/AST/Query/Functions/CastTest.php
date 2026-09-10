<?php
declare(strict_types=1);

namespace Oro\Tests\ORM\AST\Query\Functions;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\Functions\Cast;
use Oro\ORM\Query\AST\Platform\Functions\Mysql\Cast as MysqlCast;
use Oro\ORM\Query\AST\Platform\Functions\Postgresql\Cast as PostgresqlCast;
use Oro\Tests\TestCase;

class CastTest extends TestCase
{
    /**
     * @dataProvider unsupportedTypeDataProvider
     */
    public function testUnsupportedType(string $type, string $expectedMessage): void
    {
        $this->entityManager->getConfiguration()->addCustomStringFunction('cast', Cast::class);

        $query = new Query($this->entityManager);
        $query->setDQL(\sprintf('SELECT CAST(f.id AS %s) FROM Oro\Entities\Foo f', $type));

        $this->expectException(QueryException::class);
        $this->expectExceptionMessageMatches($expectedMessage);

        $query->getSQL();
    }

    public function unsupportedTypeDataProvider(): array
    {
        return [
            'unknown type' => [
                'notatype',
                '/Type notatype is not supported/'
            ],
            'type with a supported prefix' => [
                'timestamp',
                '/Type timestamp is not supported/'
            ],
            'sql appended to the length' => [
                "char('1)) IS NOT NULL AND ((SELECT 1) > 0) AND ((1=1')",
                '/Length or precision of a type to be a non-negative integer/'
            ],
            'comment appended to the length' => [
                "char('1/*comment*/')",
                '/Length or precision of a type to be a non-negative integer/'
            ],
            'sql appended to the precision' => [
                "decimal(10, '2)) OR 1=1 --')",
                '/Length or precision of a type to be a non-negative integer/'
            ],
            'not a numeric length' => [
                "char('a')",
                '/Length or precision of a type to be a non-negative integer/'
            ],
        ];
    }

    /**
     * @dataProvider splitTypeDataProvider
     */
    public function testSplitType(string $type, array $expected): void
    {
        static::assertSame($expected, Cast::splitType($type));
    }

    public function splitTypeDataProvider(): array
    {
        return [
            ['char', ['char', []]],
            ['CHAR', ['char', []]],
            ['char(1)', ['char', ['1']]],
            ['decimal(10, 2)', ['decimal', ['10', '2']]],
            ['decimal(10,2)', ['decimal', ['10', '2']]],
        ];
    }

    /**
     * @dataProvider malformedTypeDataProvider
     */
    public function testSplitTypeWithMalformedType(string $type): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('Type %s is not a valid target type.', $type));

        Cast::splitType($type);
    }

    /**
     * @dataProvider malformedTypeDataProvider
     */
    public function testMysqlCastWithMalformedType(string $type): void
    {
        $castFunction = new MysqlCast([Cast::PARAMETER_KEY => '1', Cast::TYPE_KEY => $type]);

        $this->expectException(\InvalidArgumentException::class);

        $castFunction->getSql($this->createMock(SqlWalker::class));
    }

    /**
     * @dataProvider malformedTypeDataProvider
     */
    public function testPostgresqlCastWithMalformedType(string $type): void
    {
        $castFunction = new PostgresqlCast([Cast::PARAMETER_KEY => '1', Cast::TYPE_KEY => $type]);

        $this->expectException(\InvalidArgumentException::class);

        $castFunction->getSql($this->createMock(SqlWalker::class));
    }

    public function malformedTypeDataProvider(): array
    {
        return [
            'sql appended to the type' => ['char(1)) IS NOT NULL AND ((SELECT 1) > 0) AND ((1=1'],
            'sql appended to the length' => ["char(1/*comment*/)"],
            'not a numeric length' => ['char(a)'],
            'empty type' => [''],
        ];
    }
}
