<?php

namespace Oro\ORM\Query\AST\Functions\String;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;

use Oro\ORM\Query\AST\Functions\AbstractPlatformAwareFunctionNode;

class Replace extends AbstractPlatformAwareFunctionNode
{
    const SUBJECT_KEY = 'subject';
    const FROM_KEY = 'from';
    const TO_KEY = 'to';

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->parameters[self::SUBJECT_KEY] = $parser->StringPrimary();

        $parser->match(Lexer::T_COMMA);

        $this->parameters[self::FROM_KEY] = $parser->StringPrimary();

        $parser->match(Lexer::T_COMMA);

        $this->parameters[self::TO_KEY] = $parser->StringPrimary();

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
