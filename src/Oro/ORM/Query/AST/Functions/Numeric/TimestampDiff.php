<?php

namespace Oro\ORM\Query\AST\Functions\Numeric;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;
use Oro\ORM\Query\AST\Functions\AbstractPlatformAwareFunctionNode;

class TimestampDiff extends AbstractPlatformAwareFunctionNode
{
    const UNIT_KEY = 'unit';
    const VAL1_KEY = 'val1';
    const VAL2_KEY = 'val2';

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_IDENTIFIER);
        $this->parameters[self::UNIT_KEY] = $parser->getLexer()->token['value'];
        $parser->match(Lexer::T_COMMA);
        $this->parameters[self::VAL1_KEY] = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->parameters[self::VAL2_KEY] = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
