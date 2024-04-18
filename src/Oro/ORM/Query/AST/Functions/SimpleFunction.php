<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\TokenType;

class SimpleFunction extends AbstractPlatformAwareFunctionNode
{
    public const PARAMETER_KEY = 'expression';

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->parameters[self::PARAMETER_KEY] = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
