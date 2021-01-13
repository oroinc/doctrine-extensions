<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Functions\String;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;

use Oro\ORM\Query\AST\Functions\AbstractPlatformAwareFunctionNode;

class ConcatWs extends AbstractPlatformAwareFunctionNode
{
    public const STRINGS_KEY = 'strings';
    public const SEPARATOR_KEY = 'separator';

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $this->parameters[self::SEPARATOR_KEY] = $parser->StringPrimary();

        $parser->match(Lexer::T_COMMA);

        $this->parameters[self::STRINGS_KEY][] = $parser->StringPrimary();

        while ($parser->getLexer()->isNextToken(Lexer::T_COMMA)) {
            $parser->match(Lexer::T_COMMA);
            $this->parameters[self::STRINGS_KEY][] = $parser->StringPrimary();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
