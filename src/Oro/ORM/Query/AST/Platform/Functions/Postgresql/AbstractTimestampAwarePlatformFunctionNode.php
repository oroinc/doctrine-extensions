<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Platform\Functions\Postgresql;

use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;

use Oro\ORM\Query\AST\Functions\SimpleFunction;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;

abstract class AbstractTimestampAwarePlatformFunctionNode extends PlatformFunctionNode
{
    /**
     * @param Node|string $expression
     */
    protected function getTimestampValue($expression, SqlWalker $sqlWalker): string
    {
        $value = $this->getExpressionValue($expression, $sqlWalker);
        if ($expression instanceof Literal) {
            $value = \trim(\trim($value), '\'"');
            /** @noinspection SubStrUsedAsArrayAccessInspection */
            if (\is_numeric(\substr($value, 0, 1))) {
                $timestampFunction = new Timestamp([SimpleFunction::PARAMETER_KEY => "'$value'"]);
                $value = $timestampFunction->getSql($sqlWalker);
            }
        }

        return $value;
    }
}
