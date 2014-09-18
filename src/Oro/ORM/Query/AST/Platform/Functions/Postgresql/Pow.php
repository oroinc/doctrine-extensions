<?php

namespace Oro\ORM\Query\AST\Platform\Functions\Postgresql;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;
use Oro\ORM\Query\AST\Functions\Numeric\Pow as Base;

class Pow extends PlatformFunctionNode
{
    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        /** @var Node $value */
        $value = $this->parameters[Base::VALUE_KEY];
        /** @var Node $power */
        $power = $this->parameters[Base::POWER_KEY];

        return 'POW('
            . $this->getExpressionValue($value, $sqlWalker)
            . ', '
            . $this->getExpressionValue($power, $sqlWalker)
        . ')';
    }
}
