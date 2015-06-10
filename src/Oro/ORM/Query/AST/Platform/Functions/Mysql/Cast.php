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
        if ($type == 'integer') {
            $type = 'signed';
        } elseif (in_array($type, array('string', 'text'))) {
            $type = 'char';
        }

        return 'CAST(' . $this->getExpressionValue($value, $sqlWalker) . ' AS ' . $type . ')';
    }
}
