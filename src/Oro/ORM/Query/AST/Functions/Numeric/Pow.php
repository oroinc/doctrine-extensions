<?php

namespace Oro\ORM\Query\AST\Functions\Numeric;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;
use Oro\ORM\Query\AST\Functions\AbstractPlatformAwareFunctionNode;

class Pow extends AbstractPlatformAwareFunctionNode
{
    const VALUE_KEY = 'value';
    const POWER_KEY = 'power';

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->parameters[self::VALUE_KEY] = $parser->SimpleArithmeticExpression();
        $parser->match(Lexer::T_COMMA);
        $this->parameters[self::POWER_KEY] = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
