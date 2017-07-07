<?php

namespace Oro\ORM\Query\AST\Platform\Functions\Mysql;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\Functions\String\DateFormat as BaseFunction;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;

class DateFormat extends PlatformFunctionNode
{
    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        /** @var Node $date */
        $date = $this->parameters[BaseFunction::DATE_KEY];
        /** @var Node $format */
        $format = $this->parameters[BaseFunction::FORMAT_KEY];

        return 'DATE_FORMAT('
            . $this->getExpressionValue($date, $sqlWalker)
            . ', '
            . $this->getExpressionValue($format, $sqlWalker)
        . ')';
    }
}
