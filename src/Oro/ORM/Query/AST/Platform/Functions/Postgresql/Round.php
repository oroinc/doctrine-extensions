<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Platform\Functions\Postgresql;

use Oro\ORM\Query\AST\Functions\Numeric\Round as BaseRound;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;
use Doctrine\ORM\Query\SqlWalker;

class Round extends PlatformFunctionNode
{
    public function getSql(SqlWalker $sqlWalker): string
    {
        $value = $this->parameters[BaseRound::VALUE];

        if (isset($this->parameters[BaseRound::PRECISION])) {
            $round = $this->parameters[BaseRound::PRECISION];

            return \sprintf(
                'ROUND(%s, %s)',
                $this->getExpressionValue($value, $sqlWalker),
                $this->getExpressionValue($round, $sqlWalker)
            );
        }

        return \sprintf(
            'ROUND(%s)',
            $this->getExpressionValue($value, $sqlWalker)
        );
    }
}
