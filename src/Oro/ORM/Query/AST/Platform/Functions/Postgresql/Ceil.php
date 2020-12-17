<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Platform\Functions\Postgresql;

use Oro\ORM\Query\AST\Functions\SimpleFunction;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;
use Doctrine\ORM\Query\SqlWalker;

class Ceil extends PlatformFunctionNode
{
    public function getSql(SqlWalker $sqlWalker): string
    {
        $value = $this->parameters[SimpleFunction::PARAMETER_KEY];

        return \sprintf('CEIL(%s)', $this->getExpressionValue($value, $sqlWalker));
    }
}
