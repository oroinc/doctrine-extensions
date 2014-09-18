<?php

namespace Oro\ORM\Query\AST\Platform\Functions\Mysql;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\Functions\Numeric\TimestampDiff as BaseFunction;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;

class TimestampDiff extends PlatformFunctionNode
{
    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        /** @var string $unit */
        $unit = $this->parameters[BaseFunction::UNIT_KEY];
        /** @var Node $val1 */
        $val1 = $this->parameters[BaseFunction::VAL1_KEY];
        /** @var Node $val2 */
        $val2 = $this->parameters[BaseFunction::VAL2_KEY];

        return 'TIMESTAMPDIFF('
            . $unit
            . ', '
            . $val1->dispatch($sqlWalker)
            . ', '
            . $val2->dispatch($sqlWalker)
        . ')';
    }
}