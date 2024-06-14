<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Functions\Numeric;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\TokenType;
use Oro\ORM\Query\AST\Functions\AbstractPlatformAwareFunctionNode;

class Sign extends AbstractPlatformAwareFunctionNode
{
    public const PARAMETER_KEY = 'expression';

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->parameters[self::PARAMETER_KEY] = $parser->SimpleArithmeticExpression();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
