<?php

namespace Oro\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\FunctionFactory;

abstract class AbstractPlatformAwareFunctionNode extends FunctionNode
{
    /**
     * @var array
     */
    public $parameters = array();

    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        $function = FunctionFactory::create(
            $sqlWalker->getConnection()->getDatabasePlatform()->getName(),
            $this->name,
            $this->parameters
        );
        return $function->getSql($sqlWalker);
    }
}
