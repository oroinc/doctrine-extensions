<?php

namespace Oro\ORM\Query\AST\Platform\Functions\Postgresql;

use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;

use Oro\ORM\Query\AST\Functions\SimpleFunction;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;

abstract class AbstractTimestampAwarePlatformFunctionNode extends PlatformFunctionNode
{
    /**
     * Get timestamp value for given expression.
     *
     * @param Node|string $expression
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getTimestampValue($expression, SqlWalker $sqlWalker)
    {
        $value = $this->getExpressionValue($expression, $sqlWalker);
        if ($expression instanceof Literal) {
            $value = trim(trim($value), '\'"');
            if (is_numeric(substr($value, 0, 1))) {
                $timestampFunction = new Timestamp(array(SimpleFunction::PARAMETER_KEY => "'$value'"));
                $value = $timestampFunction->getSql($sqlWalker);
            }
        }

        return $value;
    }
}
