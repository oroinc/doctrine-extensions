<?php

namespace Oro\Tests\ORM\AST;

use Doctrine\ORM\Query\QueryException;
use Oro\ORM\Query\AST\FunctionFactory;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;

class FunctionFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateException()
    {
        $this->expectException(QueryException::class);
        $this->expectExceptionMessage('[Syntax Error] Function "TestF" does not supported for platform "Test"');
        FunctionFactory::create('Test', 'TestF', array());
    }

    /**
     * @dataProvider platformFunctionsDataProvider
     * @param string $platform
     * @param string $function
     */
    public function testCreate($platform, $function)
    {
        $functionInstance = FunctionFactory::create($platform, $function, array());
        $this->assertInstanceOf(PlatformFunctionNode::class, $functionInstance);
    }

    /**
     * @return array
     */
    public function platformFunctionsDataProvider()
    {
        return array(
            array('mysql', 'date'),
            array('Mysql', 'Date'),
            array('MySql', 'DATE'),
            array('postgresql', 'group_concat'),
            array('Mysql', 'Group_Concat'),
            array('postgresql', 'GROUP_CONCAT'),
            array('Mysql', 'TimestampDiff'),
            array('postgresql', 'TIMESTAMPDIFF'),
        );
    }
}
