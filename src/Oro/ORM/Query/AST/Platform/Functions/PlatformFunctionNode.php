<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Platform\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;

abstract class PlatformFunctionNode
{
    /** @var array */
    public $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    abstract public function getSql(SqlWalker $sqlWalker): string;

    /**
     * Get expression value string.
     *
     * @param string|Node $expression
     */
    protected function getExpressionValue($expression, SqlWalker $sqlWalker): string
    {
        if ($expression instanceof Node) {
            $expression = $expression->dispatch($sqlWalker);
        }

        return $expression;
    }
}
