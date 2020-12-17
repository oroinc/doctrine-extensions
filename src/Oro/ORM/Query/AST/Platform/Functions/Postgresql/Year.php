<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Platform\Functions\Postgresql;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\Functions\SimpleFunction;

class Year extends AbstractTimestampAwarePlatformFunctionNode
{
    public function getSql(SqlWalker $sqlWalker): string
    {
        /** @var Node $expression */
        $expression = $this->parameters[SimpleFunction::PARAMETER_KEY];
        return 'EXTRACT(YEAR FROM ' . $this->getTimestampValue($expression, $sqlWalker) . ')';
    }
}
