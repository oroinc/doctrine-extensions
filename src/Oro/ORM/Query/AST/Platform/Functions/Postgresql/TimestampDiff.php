<?php

namespace Oro\ORM\Query\AST\Platform\Functions\Postgresql;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\Functions\Numeric\TimestampDiff as BaseFunction;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;

class Timestampdiff extends PlatformFunctionNode
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

        $expr = sprintf(
            'CAST(ROUND(%s) AS INT)',
            $this->getDiffExpr(strtoupper($unit), $val1->dispatch($sqlWalker), $val2->dispatch($sqlWalker))
        );

        return $expr;
    }

    /**
     * @param string $unit
     * @param string $val1
     * @param string $val2
     *
     * @return string
     *
     * @throws DBALException
     */
    protected function getDiffExpr($unit, $val1, $val2)
    {
        switch ($unit) {
            case 'MICROSECOND':
                return sprintf(
                    '(EXTRACT(EPOCH FROM CAST(%s AS timestamp)) - EXTRACT(EPOCH FROM CAST(%s AS timestamp))) * 1000000',
                    $val2,
                    $val1
                );
            case 'SECOND':
                return sprintf(
                    '(EXTRACT(EPOCH FROM CAST(%s AS timestamp)) - EXTRACT(EPOCH FROM CAST(%s AS timestamp)))',
                    $val2,
                    $val1
                );
            case 'MINUTE':
                return sprintf(
                    '(EXTRACT(EPOCH FROM CAST(%s AS timestamp)) - EXTRACT(EPOCH FROM CAST(%s AS timestamp))) / 60',
                    $val2,
                    $val1
                );
            case 'HOUR':
                return sprintf(
                    '(EXTRACT(EPOCH FROM CAST(%s AS timestamp)) - EXTRACT(EPOCH FROM CAST(%s AS timestamp))) / 3600',
                    $val2,
                    $val1
                );
            case 'DAY':
                return sprintf('EXTRACT(DAY FROM CAST(%s AS timestamp) - CAST(%s AS timestamp))', $val2, $val1);
            case 'WEEK':
                return sprintf('EXTRACT(DAY FROM CAST(%s AS timestamp) - CAST(%s AS timestamp)) / 7', $val2, $val1);
            case 'MONTH':
                return
                    sprintf(
                        'CAST(EXTRACT(YEAR FROM CAST(%s AS timestamp))'
                        . ' - EXTRACT(YEAR FROM CAST(%s AS timestamp)) AS INT)',
                        $val2,
                        $val1
                    )
                    . ' * 12 + '
                    . sprintf(
                        'EXTRACT(MONTH FROM CAST(%s AS timestamp)) - EXTRACT(MONTH FROM CAST(%s AS timestamp))',
                        $val2,
                        $val1
                    );
            case 'QUARTER':
                return
                    sprintf(
                        'CAST(EXTRACT(YEAR FROM CAST(%s AS timestamp))'
                        . ' - EXTRACT(YEAR FROM CAST(%s AS timestamp)) AS INT)',
                        $val2,
                        $val1
                    )
                    . ' * 4 + '
                    . sprintf(
                        'EXTRACT(QUARTER FROM CAST(%s AS timestamp)) - EXTRACT(QUARTER FROM CAST(%s AS timestamp))',
                        $val2,
                        $val1
                    );
            case 'YEAR':
                return sprintf(
                    'EXTRACT(YEAR FROM CAST(%s AS timestamp)) - EXTRACT(YEAR FROM CAST(%s AS timestamp))',
                    $val2,
                    $val1
                );
            default:
                throw new DBALException("Unit '$unit' is not valid for TIMESTAMPDIFF function.");
        }
    }
}
