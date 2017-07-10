<?php

namespace Oro\ORM\Query\AST\Platform\Functions\Postgresql;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\Functions\Cast as CastDQL;
use Oro\ORM\Query\AST\Functions\Numeric\TimestampDiff as BaseFunction;

class Timestampdiff extends AbstractTimestampAwarePlatformFunctionNode
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

        return $this->getSqlByUnit($unit, $firstDateNode, $secondDateNode, $sqlWalker);
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
        return sprintf(
            '(EXTRACT(EPOCH FROM %s) - EXTRACT(EPOCH FROM %s))',
            $this->getTimestampValue($secondDateNode, $sqlWalker),
            $this->getTimestampValue($firstDateNode, $sqlWalker)
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
        return $this->getRoundedValue(
            $this->getDiffForSecond($firstDateNode, $secondDateNode, $sqlWalker) . ' * 1000000',
            $sqlWalker
        );
    }

    /**
     * @param Node $firstDateNode
     * @param Node $secondDateNode
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getDiffForMinute(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker)
    {
        return $this->getRoundedValue(
            $this->getDiffForSecond($firstDateNode, $secondDateNode, $sqlWalker) . ' / 60',
            $sqlWalker
        );
    }

    /**
     * @param Node $firstDateNode
     * @param Node $secondDateNode
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getDiffForHour(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker)
    {
        return $this->getRoundedValue(
            $this->getDiffForSecond($firstDateNode, $secondDateNode, $sqlWalker) . ' / 3600',
            $sqlWalker
        );
    }

    /**
     * @param Node $firstDateNode
     * @param Node $secondDateNode
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getDiffForDay(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker)
    {
        return sprintf(
            'EXTRACT(DAY FROM %s - %s)',
            $this->getTimestampValue($secondDateNode, $sqlWalker),
            $this->getTimestampValue($firstDateNode, $sqlWalker)
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
        return $this->getRoundedValue(
            $this->getDiffForDay($firstDateNode, $secondDateNode, $sqlWalker) . ' / 7',
            $sqlWalker
        );
    }

    /**
     * @param Node $firstDateNode
     * @param Node $secondDateNode
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getDiffForYear(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker)
    {
        $firstDateTimestamp = $this->getTimestampValue($firstDateNode, $sqlWalker);
        $secondDateTimestamp = $this->getTimestampValue($secondDateNode, $sqlWalker);

        return sprintf('EXTRACT(YEAR from %s)', $this->getAge($firstDateTimestamp, $secondDateTimestamp));
    }

    /**
     * @param Node $firstDateNode
     * @param Node $secondDateNode
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getDiffForMonth(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker)
    {
        $firstDateTimestamp = $this->getTimestampValue($firstDateNode, $sqlWalker);
        $secondDateTimestamp = $this->getTimestampValue($secondDateNode, $sqlWalker);

        $months = sprintf(
            'EXTRACT(MONTH from %s)',
            $this->getAge($firstDateTimestamp, $secondDateTimestamp)
        );

        return sprintf(
            '(%s * 12 + %s)',
            $this->getDiffForYear($firstDateNode, $secondDateNode, $sqlWalker),
            $months
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
        return $this->getRoundedValue(
            $this->getDiffForMonth($firstDateNode, $secondDateNode, $sqlWalker) . ' / 3',
            $sqlWalker
        );
    }

    /**
     * @param string $firstDateTimestamp
     * @param string $secondDateTimestamp
     * @return string
     */
    protected function getAge($firstDateTimestamp, $secondDateTimestamp)
    {
        return sprintf('age(%s, %s)', $secondDateTimestamp, $firstDateTimestamp);
    }

    /**
     * @param string $value
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getRoundedValue($value, SqlWalker $sqlWalker)
    {
        return $this->castAs(sprintf('ROUND(%s)', $value), 'int', $sqlWalker);
    }

    /**
     * @param string|Node $value
     * @param string $type
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function castAs($value, $type, SqlWalker $sqlWalker)
    {
        $cast = new Cast(array(CastDQL::PARAMETER_KEY => $value, CastDQL::TYPE_KEY => $type));

        return $cast->getSql($sqlWalker);
    }
}
