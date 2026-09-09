<?php

namespace Oro\Tests\ORM\AST\Query\Functions;

use Doctrine\ORM\Query;
use Oro\ORM\Query\AST\Functions\Cast;
use Oro\ORM\Query\AST\Platform\Functions\Mysql\Cast as MysqlCast;
use Oro\ORM\Query\AST\Platform\Functions\Postgresql\Cast as PostgresqlCast;
use Oro\Tests\TestCase;

class CastTest extends TestCase
{
    /**
     * @dataProvider unsupportedTypeDataProvider
     * @param string $type
     * @param string $expectedMessage
     */
    public function testUnsupportedType($type, $expectedMessage)
    {
        $this->entityManager->getConfiguration()
            ->addCustomStringFunction('cast', 'Oro\ORM\Query\AST\Functions\Cast');

        $query = new Query($this->entityManager);
        $query->setDQL(sprintf('SELECT CAST(f.id AS %s) FROM Oro\Entities\Foo f', $type));

        $this->setExpectedExceptionRegExp('Doctrine\ORM\Query\QueryException', $expectedMessage);

        $query->getSQL();
    }

    /**
     * @return array
     */
    public function unsupportedTypeDataProvider()
    {
        return [
            'unknown type' => [
                'notatype',
                '/Type unsupported/'
            ],
            'type with a supported prefix' => [
                'timestamp',
                '/Type unsupported/'
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
     * @param string $type
     * @param array $expected
     */
    public function testSplitType($type, array $expected)
    {
        $this->assertSame($expected, Cast::splitType($type));
    }

    /**
     * @return array
     */
    public function splitTypeDataProvider()
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
     * @param string $type
     */
    public function testSplitTypeWithMalformedType($type)
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            sprintf('Type %s is not a valid target type.', $type)
        );

        Cast::splitType($type);
    }

    /**
     * @dataProvider malformedTypeDataProvider
     * @param string $type
     */
    public function testMysqlCastWithMalformedType($type)
    {
        $castFunction = new MysqlCast([Cast::PARAMETER_KEY => '1', Cast::TYPE_KEY => $type]);

        $this->setExpectedException('InvalidArgumentException');

        $castFunction->getSql($this->getSqlWalkerMock());
    }

    /**
     * @dataProvider malformedTypeDataProvider
     * @param string $type
     */
    public function testPostgresqlCastWithMalformedType($type)
    {
        $castFunction = new PostgresqlCast([Cast::PARAMETER_KEY => '1', Cast::TYPE_KEY => $type]);

        $this->setExpectedException('InvalidArgumentException');

        $castFunction->getSql($this->getSqlWalkerMock());
    }

    /**
     * @return array
     */
    public function malformedTypeDataProvider()
    {
        return [
            'sql appended to the type' => ['char(1)) IS NOT NULL AND ((SELECT 1) > 0) AND ((1=1'],
            'sql appended to the length' => ['char(1/*comment*/)'],
            'not a numeric length' => ['char(a)'],
            'empty type' => [''],
        ];
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getSqlWalkerMock()
    {
        return $this->getMockBuilder('Doctrine\ORM\Query\SqlWalker')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
