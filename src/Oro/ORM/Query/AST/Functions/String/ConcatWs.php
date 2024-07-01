<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Functions\String;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\TokenType;

use Oro\ORM\Query\AST\Functions\AbstractPlatformAwareFunctionNode;

class ConcatWs extends AbstractPlatformAwareFunctionNode
{
    public const STRINGS_KEY = 'strings';
    public const SEPARATOR_KEY = 'separator';

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->parameters[self::SEPARATOR_KEY] = $parser->StringPrimary();

        $parser->match(TokenType::T_COMMA);

        $this->parameters[self::STRINGS_KEY][] = $parser->StringPrimary();

        while ($parser->getLexer()->isNextToken(TokenType::T_COMMA)) {
            $parser->match(TokenType::T_COMMA);
            $this->parameters[self::STRINGS_KEY][] = $parser->StringPrimary();
        }

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
