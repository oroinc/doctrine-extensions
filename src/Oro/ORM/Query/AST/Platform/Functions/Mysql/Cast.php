<?php

namespace Oro\ORM\Query\AST\Platform\Functions\Mysql;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\Functions\Cast as DqlFunction;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;

class Cast extends PlatformFunctionNode
{
    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        /** @var Node $value */
        $value = $this->parameters[DqlFunction::PARAMETER_KEY];
        list($type, $arguments) = DqlFunction::splitType($this->parameters[DqlFunction::TYPE_KEY]);

        $isBoolean = $type === 'bool' || $type === 'boolean';
        if ($type === 'char') {
            $arguments = $arguments ?: ['1'];
        } elseif ($type === 'string' || $type === 'text' || $type === 'json') {
            $type = 'char';
        } elseif ($type === 'int' || $type === 'integer' || $isBoolean) {
            $type = 'signed';
        }

        if ($arguments) {
            $type .= '(' . implode(', ', $arguments) . ')';
        }

        $expression = 'CAST(' . $this->getExpressionValue($value, $sqlWalker) . ' AS ' . $type . ')';

        if ($isBoolean) {
            $expression .= ' <> 0';
        }

        return $expression;
    }
}
