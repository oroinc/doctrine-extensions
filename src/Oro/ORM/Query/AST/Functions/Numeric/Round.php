<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Functions\Numeric;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\TokenType;
use Oro\ORM\Query\AST\Functions\AbstractPlatformAwareFunctionNode;

class Round extends AbstractPlatformAwareFunctionNode
{
    public const VALUE = 'value';
    public const PRECISION = 'precision';

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser): void
    {
        $lexer = $parser->getLexer();
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->parameters[self::VALUE] = $parser->SimpleArithmeticExpression();

        // parse second parameter if available
        if (TokenType::T_COMMA === $lexer->lookahead->type) {
            $parser->match(TokenType::T_COMMA);
            $this->parameters[self::PRECISION] = $parser->ArithmeticPrimary();
        }

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
