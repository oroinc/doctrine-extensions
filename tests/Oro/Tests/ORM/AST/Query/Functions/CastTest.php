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
        return array(
            'unknown type' => array(
                'notatype',
                '/Type unsupported/'
            ),
            'type with a supported prefix' => array(
                'timestamp',
                '/Type unsupported/'
            ),
            'sql appended to the length' => array(
                "char('1)) IS NOT NULL AND ((SELECT 1) > 0) AND ((1=1')",
                '/Length or precision of a type to be a non-negative integer/'
            ),
            'comment appended to the length' => array(
                "char('1/*comment*/')",
                '/Length or precision of a type to be a non-negative integer/'
            ),
            'sql appended to the precision' => array(
                "decimal(10, '2)) OR 1=1 --')",
                '/Length or precision of a type to be a non-negative integer/'
            ),
            'not a numeric length' => array(
                "char('a')",
                '/Length or precision of a type to be a non-negative integer/'
            ),
        );
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
        return array(
            array('char', array('char', array())),
            array('CHAR', array('char', array())),
            array('char(1)', array('char', array('1'))),
            array('decimal(10, 2)', array('decimal', array('10', '2'))),
            array('decimal(10,2)', array('decimal', array('10', '2'))),
        );
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
        $castFunction = new MysqlCast(
            array(Cast::PARAMETER_KEY => '1', Cast::TYPE_KEY => $type)
        );

        $this->setExpectedException('InvalidArgumentException');

        $castFunction->getSql($this->getSqlWalkerMock());
    }

    /**
     * @dataProvider malformedTypeDataProvider
     * @param string $type
     */
    public function testPostgresqlCastWithMalformedType($type)
    {
        $castFunction = new PostgresqlCast(
            array(Cast::PARAMETER_KEY => '1', Cast::TYPE_KEY => $type)
        );

        $this->setExpectedException('InvalidArgumentException');

        $castFunction->getSql($this->getSqlWalkerMock());
    }

    /**
     * @return array
     */
    public function malformedTypeDataProvider()
    {
        return array(
            'sql appended to the type' => array('char(1)) IS NOT NULL AND ((SELECT 1) > 0) AND ((1=1'),
            'sql appended to the length' => array('char(1/*comment*/)'),
            'not a numeric length' => array('char(a)'),
            'empty type' => array(''),
        );
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
