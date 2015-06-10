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
        $type  = $this->parameters[DqlFunction::TYPE_KEY];

        $type = strtolower($type);
        if ($type == 'char') {
            $type = 'char(1)';
        } elseif ($type == 'string' || $type == 'text') {
            $type = 'char';
        } elseif ($type == 'int' || $type == 'integer') {
            $type = 'signed';
        }

        return 'CAST(' . $this->getExpressionValue($value, $sqlWalker) . ' AS ' . $type . ')';
    }
}
