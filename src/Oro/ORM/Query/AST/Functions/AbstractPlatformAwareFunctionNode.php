<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\FunctionFactory;

abstract class AbstractPlatformAwareFunctionNode extends FunctionNode
{
    public array $parameters = [];

    /**
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker): string
    {
        $function = FunctionFactory::create(
            $sqlWalker->getConnection()->getDatabasePlatform(),
            $this->name,
            $this->parameters
        );
        return $function->getSql($sqlWalker);
    }
}
