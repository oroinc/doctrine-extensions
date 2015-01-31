<?php

namespace Oro\ORM\Query\AST\Platform\Functions\Sqlite;

use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\Functions\SimpleFunction;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;

class Month extends PlatformFunctionNode
{
    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $expression = $this->parameters[SimpleFunction::PARAMETER_KEY];

        return sprintf(
            'strftime(\'%s\', %s)',
            'm',
            $this->getExpressionValue($expression, $sqlWalker)
        );
    }
}
