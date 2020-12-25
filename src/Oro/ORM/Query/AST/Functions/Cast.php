<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;

class Cast extends AbstractPlatformAwareFunctionNode
{
    public const PARAMETER_KEY = 'expression';
    public const TYPE_KEY = 'type';

    /** @var array */
    protected $supportedTypes = [
        'char',
        'string',
        'text',
        'date',
        'datetime',
        'time',
        'int',
        'integer',
        'bigint',
        'decimal',
        'json',
        'bool',
        'boolean',
        'binary'
    ];

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->parameters[self::PARAMETER_KEY] = $parser->ArithmeticExpression();

        $parser->match(Lexer::T_AS);

        $parser->match(Lexer::T_IDENTIFIER);
        $lexer = $parser->getLexer();
        $type = $lexer->token['value'];

        if ($lexer->isNextToken(Lexer::T_OPEN_PARENTHESIS)) {
            $parser->match(Lexer::T_OPEN_PARENTHESIS);
            $parameter = $parser->Literal();
            $parameters = [
                $parameter->value
            ];
            if ($lexer->isNextToken(Lexer::T_COMMA)) {
                while ($lexer->isNextToken(Lexer::T_COMMA)) {
                    $parser->match(Lexer::T_COMMA);
                    $parameter = $parser->Literal();
                    $parameters[] = $parameter->value;
                }
            }
            $parser->match(Lexer::T_CLOSE_PARENTHESIS);
            $type .= '(' . \implode(', ', $parameters) . ')';
        }

        if (!$this->isSupportedType($type)) {
            $parser->syntaxError(
                \sprintf(
                    'Type %s is not supported. The supported types are: "%s"',
                    $type,
                    \implode(', ', $this->supportedTypes)
                ),
                $lexer->token
            );
        }

        $this->parameters[self::TYPE_KEY] = $type;

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    protected function isSupportedType(string $type): bool
    {
        $type = \strtolower(\trim($type));
        foreach ($this->supportedTypes as $supportedType) {
            if (0 === \strpos($type, $supportedType)) {
                return true;
            }
        }

        return false;
    }
}
