<?php

namespace Oro\ORM\Query\AST\Functions\DateTime;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;
use Oro\ORM\Query\AST\Functions\AbstractPlatformAwareFunctionNode;

class ConvertTz extends AbstractPlatformAwareFunctionNode
{
    const VALUE_KEY = 'value';
    const FROM_TZ_KEY = 'from_tz';
    const TO_TZ_KEY = 'to_tz';

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->parameters[self::VALUE_KEY] = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->parameters[self::FROM_TZ_KEY] = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->parameters[self::TO_TZ_KEY] = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
