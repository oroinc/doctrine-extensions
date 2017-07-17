<?php

namespace Oro\ORM\Query\AST\Platform\Functions\Postgresql;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\Functions\String\DateFormat as BaseFunction;

class DateFormat extends AbstractTimestampAwarePlatformFunctionNode
{
    /**
     * @var array
     */
    private static $map = [
        '%a' => 'Dy',
        '%b' => 'Mon',
        '%c' => 'FMMM',
        '%d' => 'DD',
        '%e' => 'FMDD',
        '%f' => 'US',
        '%H' => 'HH24',
        '%h' => 'HH12',
        '%I' => 'HH12',
        '%i' => 'MI',
        '%j' => 'DDD',
        '%k' => 'FMHH24',
        '%l' => 'FMHH12',
        '%M' => 'FMMonth',
        '%m' => 'MM',
        '%p' => 'AM',
        '%r' => 'HH12:MI:SS AM',
        '%S' => 'SS',
        '%s' => 'SS',
        '%T' => 'HH24:MI:SS',
        '%W' => 'FMDay',
        '%Y' => 'YYYY',
        '%y' => 'YY',
        '%%' => '%',
    ];

    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        /** @var Node $date */
        $date = $this->parameters[BaseFunction::DATE_KEY];
        /** @var Node $format */
        $format = $this->parameters[BaseFunction::FORMAT_KEY];

        return 'to_char('
                . $this->getTimestampValue($date, $sqlWalker) . ', '
                . $this->getPostgresFormat($this->getExpressionValue($format, $sqlWalker))
            . ')';
    }

    /**
     * @param string $format
     * @return string
     */
    private function getPostgresFormat($format)
    {
        return strtr($format, self::$map);
    }
}
