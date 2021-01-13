<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Platform\Functions\Postgresql;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\Functions\Numeric\TimestampDiff as BaseFunction;

class Timestampdiff extends AbstractTimestampAwarePlatformFunctionNode
{
    public function getSql(SqlWalker $sqlWalker): string
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
     */
    protected function getSqlByUnit(
        string $unit,
        Node $firstDateNode,
        Node $secondDateNode,
        SqlWalker $sqlWalker
    ): string {
        $method = 'getDiffFor' . \ucfirst(\strtolower($unit));

        return \call_user_func([$this, $method], $firstDateNode, $secondDateNode, $sqlWalker);
    }

    protected function getDiffForMicrosecond(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string
    {
        return \sprintf(
            'EXTRACT(MICROSECOND FROM %s - %s)',
            $this->getTimestampValue($secondDateNode, $sqlWalker),
            $this->getTimestampValue($firstDateNode, $sqlWalker)
        );
    }

    protected function getDiffForSecond(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string
    {
        return $this->getFloorValue(
            $this->getRawDiffForSecond($firstDateNode, $secondDateNode, $sqlWalker)
        );
    }

    protected function getDiffForMinute(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string
    {
        return $this->getFloorValue(
            $this->getRawDiffForSecond($firstDateNode, $secondDateNode, $sqlWalker) . ' / 60'
        );
    }

    protected function getDiffForHour(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string
    {
        return $this->getFloorValue(
            $this->getRawDiffForSecond($firstDateNode, $secondDateNode, $sqlWalker) . ' / 3600'
        );
    }

    protected function getDiffForDay(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string
    {
        return \sprintf(
            'EXTRACT(DAY FROM %s - %s)',
            $this->getTimestampValue($secondDateNode, $sqlWalker),
            $this->getTimestampValue($firstDateNode, $sqlWalker)
        );
    }

    protected function getDiffForWeek(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string
    {
        return $this->getFloorValue(
            $this->getDiffForDay($firstDateNode, $secondDateNode, $sqlWalker) . ' / 7'
        );
    }

    protected function getDiffForMonth(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string
    {
        $firstDateTimestamp = $this->getTimestampValue($firstDateNode, $sqlWalker);
        $secondDateTimestamp = $this->getTimestampValue($secondDateNode, $sqlWalker);

        $months = \sprintf(
            'EXTRACT(MONTH from %s)',
            $this->getAge($firstDateTimestamp, $secondDateTimestamp)
        );

        return \sprintf(
            '(%s * 12 + %s)',
            $this->getDiffForYear($firstDateNode, $secondDateNode, $sqlWalker),
            $months
        );
    }

    protected function getDiffForQuarter(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string
    {
        return $this->getFloorValue(
            $this->getDiffForMonth($firstDateNode, $secondDateNode, $sqlWalker) . ' / 3'
        );
    }

    protected function getDiffForYear(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string
    {
        $firstDateTimestamp = $this->getTimestampValue($firstDateNode, $sqlWalker);
        $secondDateTimestamp = $this->getTimestampValue($secondDateNode, $sqlWalker);

        return \sprintf('EXTRACT(YEAR from %s)', $this->getAge($firstDateTimestamp, $secondDateTimestamp));
    }

    protected function getAge(string $firstDateTimestamp, string $secondDateTimestamp): string
    {
        return \sprintf('age(%s, %s)', $secondDateTimestamp, $firstDateTimestamp);
    }

    protected function getFloorValue(string $value): string
    {
        return \sprintf('FLOOR(%s)', $value);
    }

    protected function getRawDiffForSecond(Node $firstDateNode, Node $secondDateNode, SqlWalker $sqlWalker): string
    {
        return \sprintf(
            'EXTRACT(EPOCH FROM %s - %s)',
            $this->getTimestampValue($secondDateNode, $sqlWalker),
            $this->getTimestampValue($firstDateNode, $sqlWalker)
        );
    }
}
