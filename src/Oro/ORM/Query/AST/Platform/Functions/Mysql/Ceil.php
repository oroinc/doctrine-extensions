<?php

namespace Oro\ORM\Query\AST\Platform\Functions\Mysql;

use Oro\ORM\Query\AST\Functions\SimpleFunction;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;
use Doctrine\ORM\Query\SqlWalker;

class Ceil extends PlatformFunctionNode
{
    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $value = $this->parameters[SimpleFunction::PARAMETER_KEY];

        return sprintf('CEIL(%s)', $this->getExpressionValue($value, $sqlWalker));
    }
}
