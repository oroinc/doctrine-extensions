<?php

namespace Oro\ORM\Query\AST\Platform\Functions;

use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;

abstract class PlatformFunctionNode
{
    /**
     * @var array
     */
    public $parameters;

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param SqlWalker $sqlWalker
     *
     * @return string
     */
    abstract public function getSql(SqlWalker $sqlWalker);

    /**
     * Get expression value string.
     *
     * @param string|Node $expression
     * @param SqlWalker $sqlWalker
     * @return string
     */
    protected function getExpressionValue($expression, SqlWalker $sqlWalker)
    {
        if ($expression instanceof Node) {
            $expression = $expression->dispatch($sqlWalker);
        }

        return $expression;
    }
}
