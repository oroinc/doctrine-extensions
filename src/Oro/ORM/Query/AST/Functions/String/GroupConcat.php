<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Functions\String;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\TokenType;
use Oro\ORM\Query\AST\Functions\AbstractPlatformAwareFunctionNode;

class GroupConcat extends AbstractPlatformAwareFunctionNode
{
    public const PARAMETER_KEY = 'expression';
    public const ORDER_KEY = 'order';
    public const SEPARATOR_KEY = 'separator';
    public const DISTINCT_KEY = 'distinct';

    /**
     * @url http://sysmagazine.com/posts/181666/
     * {@inheritdoc}
     */
    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $lexer = $parser->getLexer();
        if ($lexer->isNextToken(TokenType::T_DISTINCT)) {
            $parser->match(TokenType::T_DISTINCT);

            $this->parameters[self::DISTINCT_KEY] = true;
        }

        // first Path Expression is mandatory
        $this->parameters[self::PARAMETER_KEY] = [];
        $this->parameters[self::PARAMETER_KEY][] = $parser->StringPrimary();

        while ($lexer->isNextToken(TokenType::T_COMMA)) {
            $parser->match(TokenType::T_COMMA);
            $this->parameters[self::PARAMETER_KEY][] = $parser->StringPrimary();
        }

        if ($lexer->isNextToken(TokenType::T_ORDER)) {
            $this->parameters[self::ORDER_KEY] = $parser->OrderByClause();
        }

        if ($lexer->isNextToken(TokenType::T_IDENTIFIER)) {
            if (\strtolower($lexer->lookahead->value) !== 'separator') {
                $parser->syntaxError('separator');
            }
            $parser->match(TokenType::T_IDENTIFIER);

            $this->parameters[self::SEPARATOR_KEY] = $parser->StringPrimary();
        }

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
