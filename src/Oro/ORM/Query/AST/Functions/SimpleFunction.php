<?php

namespace Oro\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;

class SimpleFunction extends AbstractPlatformAwareFunctionNode
{
    const PARAMETER_KEY = 'expression';

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->parameters[self::PARAMETER_KEY] = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
