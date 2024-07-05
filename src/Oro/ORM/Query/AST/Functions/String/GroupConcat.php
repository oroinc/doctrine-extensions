<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Functions\String;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;
use Oro\ORM\Query\AST\Functions\AbstractPlatformAwareFunctionNode;

class GroupConcat extends AbstractPlatformAwareFunctionNode
{
    public const PARAMETER_KEY = 'expression';
    public const ORDER_KEY = 'order';
    public const SEPARATOR_KEY = 'separator';
    public const DISTINCT_KEY = 'distinct';

    /**
     * @url http://sysmagazine.com/posts/181666/
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        $lexer = $parser->getLexer();
        if ($lexer->isNextToken(Lexer::T_DISTINCT)) {
            $parser->match(Lexer::T_DISTINCT);

            $this->parameters[self::DISTINCT_KEY] = true;
        }

        // first Path Expression is mandatory
        $this->parameters[self::PARAMETER_KEY] = [];
        $this->parameters[self::PARAMETER_KEY][] = $parser->StringPrimary();

        while ($lexer->isNextToken(Lexer::T_COMMA)) {
            $parser->match(Lexer::T_COMMA);
            $this->parameters[self::PARAMETER_KEY][] = $parser->StringPrimary();
        }

        if ($lexer->isNextToken(Lexer::T_ORDER)) {
            $this->parameters[self::ORDER_KEY] = $parser->OrderByClause();
        }

        if ($lexer->isNextToken(Lexer::T_IDENTIFIER)) {
            if (\strtolower($lexer->lookahead->value) !== 'separator') {
                $parser->syntaxError('separator');
            }
            $parser->match(Lexer::T_IDENTIFIER);

            $this->parameters[self::SEPARATOR_KEY] = $parser->StringPrimary();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
