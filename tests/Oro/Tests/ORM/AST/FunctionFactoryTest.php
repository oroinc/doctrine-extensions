<?php
declare(strict_types=1);

namespace Oro\Tests\ORM\AST;

use Doctrine\ORM\Query\QueryException;
use Oro\ORM\Query\AST\FunctionFactory;
use PHPUnit\Framework\TestCase;

class FunctionFactoryTest extends TestCase
{
    public function testCreateException(): void
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('[Syntax Error] Function "TestF" does not supported for platform "Test"');
        FunctionFactory::create('Test', 'TestF', []);
    }

    /**
     * @dataProvider platformFunctionsDataProvider
     * @throws QueryException
     */
    public function testCreate(string $platform, string $function): void
    {
        $this->expectNotToPerformAssertions();
        FunctionFactory::create($platform, $function, []);
    }

    public function platformFunctionsDataProvider(): array
    {
        return [
            ['mysql', 'date'],
            ['Mysql', 'Date'],
            ['MySql', 'DATE'],
            ['postgresql', 'group_concat'],
            ['Mysql', 'Group_Concat'],
            ['postgresql', 'GROUP_CONCAT'],
            ['Mysql', 'TimestampDiff'],
            ['postgresql', 'TIMESTAMPDIFF'],
        ];
    }
}
