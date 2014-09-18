<?php

namespace Oro\ORM\Query\AST\Platform\Functions\Postgresql;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;

use Oro\ORM\Query\AST\Functions\Cast as CastDQL;
use Oro\ORM\Query\AST\Functions\Numeric\TimestampDiff as BaseFunction;
use Oro\ORM\Query\AST\Functions\SimpleFunction;
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
        /** @var Node $firstDateNode */
        $firstDateNode = $this->parameters[BaseFunction::VAL1_KEY];
        /** @var Node $secondDateNode */
        $secondDateNode = $this->parameters[BaseFunction::VAL2_KEY];

        $castFunction = new Cast(
            array(
                CastDQL::PARAMETER_KEY => sprintf(
                    'ROUND(%s)',
                    $this->getSqlByUnit($unit, $firstDateNode, $secondDateNode, $sqlWalker)
                ),
                CastDQL::TYPE_KEY => 'INT'
            )
        );

        return $castFunction->getSql($sqlWalker);
    }

    /**
     * Get TimestampDiff expression by unit.
     *
     * @param string $unit
     * @param Node $firstDateNode
     * @param Node $secondDateNode
     * @param SqlWalker $sqlWalker
     *
     * @return string
     */
    protected function getSqlByUnit($unit, Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker)
    {
        $method = 'getDiffFor' . ucfirst(strtolower($unit));
        return call_user_func(array($this, $method), $firstDateNode, $secondDateNode, $sqlWalker);
    }

    /**
     * @param Node $firstDateNode
     * @param Node $secondDateNode
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getDiffForSecond(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker)
    {
        $firstDateTimestampFunction = new Timestamp(array(SimpleFunction::PARAMETER_KEY => $firstDateNode));
        $secondDateTimestampFunction = new Timestamp(array(SimpleFunction::PARAMETER_KEY => $secondDateNode));

        return sprintf(
            '(EXTRACT(EPOCH FROM %s) - EXTRACT(EPOCH FROM %s))',
            $secondDateTimestampFunction->getSql($sqlWalker),
            $firstDateTimestampFunction->getSql($sqlWalker)
        );
    }

    /**
     * @param Node $firstDateNode
     * @param Node $secondDateNode
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getDiffForMicrosecond(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker)
    {
        return $this->getDiffForSecond($firstDateNode, $secondDateNode, $sqlWalker) . ' * 1000000';
    }

    /**
     * @param Node $firstDateNode
     * @param Node $secondDateNode
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getDiffForMinute(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker)
    {
        return $this->getDiffForSecond($firstDateNode, $secondDateNode, $sqlWalker) . ' / 60';
    }

    /**
     * @param Node $firstDateNode
     * @param Node $secondDateNode
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getDiffForHour(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker)
    {
        return $this->getDiffForSecond($firstDateNode, $secondDateNode, $sqlWalker) . ' / 3600';
    }

    /**
     * @param Node $firstDateNode
     * @param Node $secondDateNode
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getDiffForDay(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker)
    {
        $firstDateTimestampFunction = new Timestamp(array(SimpleFunction::PARAMETER_KEY => $firstDateNode));
        $secondDateTimestampFunction = new Timestamp(array(SimpleFunction::PARAMETER_KEY => $secondDateNode));

        return sprintf(
            'EXTRACT(DAY FROM %s - %s)',
            $secondDateTimestampFunction->getSql($sqlWalker),
            $firstDateTimestampFunction->getSql($sqlWalker)
        );
    }

    /**
     * @param Node $firstDateNode
     * @param Node $secondDateNode
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getDiffForWeek(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker)
    {
        return $this->getDiffForDay($firstDateNode, $secondDateNode, $sqlWalker) . ' / 7';
    }

    /**
     * @param Node $firstDateNode
     * @param Node $secondDateNode
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getDiffForYear(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker)
    {
        $firstDateYearFunction = new Year(array(SimpleFunction::PARAMETER_KEY => $firstDateNode));
        $secondDateYearFunction = new Year(array(SimpleFunction::PARAMETER_KEY => $secondDateNode));

        return sprintf(
            '(%s - %s)',
            $secondDateYearFunction->getSql($sqlWalker),
            $firstDateYearFunction->getSql($sqlWalker)
        );
    }

    /**
     * @param Node $firstDateNode
     * @param Node $secondDateNode
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getDiffForMonth(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker)
    {
        $firstDateMonthFunction = new Month(array(SimpleFunction::PARAMETER_KEY => $firstDateNode));
        $secondDateMonthFunction = new Month(array(SimpleFunction::PARAMETER_KEY => $secondDateNode));

        return sprintf(
            '%s * 12 + (%s - %s)',
            $this->getDiffForYear($firstDateNode, $secondDateNode, $sqlWalker),
            $secondDateMonthFunction->getSql($sqlWalker),
            $firstDateMonthFunction->getSql($sqlWalker)
        );
    }

    /**
     * @param Node $firstDateNode
     * @param Node $secondDateNode
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getDiffForQuarter(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker)
    {
        $firstDateQuarterFunction = new Quarter(array(SimpleFunction::PARAMETER_KEY => $firstDateNode));
        $secondDateQuarterFunction = new Quarter(array(SimpleFunction::PARAMETER_KEY => $secondDateNode));

        return sprintf(
            '%s * 4 + (%s - %s)',
            $this->getDiffForYear($firstDateNode, $secondDateNode, $sqlWalker),
            $secondDateQuarterFunction->getSql($sqlWalker),
            $firstDateQuarterFunction->getSql($sqlWalker)
        );
    }
}
