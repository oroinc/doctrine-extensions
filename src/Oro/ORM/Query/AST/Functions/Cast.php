<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;

class Cast extends AbstractPlatformAwareFunctionNode
{
    public const PARAMETER_KEY = 'expression';
    public const TYPE_KEY = 'type';

    /**
     * A target type: a type name, optionally followed by a length or a precision.
     */
    private const TYPE_PATTERN = '/^(?P<type>[a-z]+)(?:\((?P<arguments>\d+(?:, ?\d+)*)\))?$/';

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
        'binary',
        'uuid'
    ];

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->parameters[self::PARAMETER_KEY] = $parser->ArithmeticExpression();

        $parser->match(Lexer::T_AS);

        $parser->match(Lexer::T_IDENTIFIER);
        $lexer = $parser->getLexer();
        $type = $lexer->token->value;

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

        if ($lexer->isNextToken(Lexer::T_OPEN_PARENTHESIS)) {
            $parser->match(Lexer::T_OPEN_PARENTHESIS);
            $parameters = [
                $this->matchTypeArgument($parser)
            ];
            while ($lexer->isNextToken(Lexer::T_COMMA)) {
                $parser->match(Lexer::T_COMMA);
                $parameters[] = $this->matchTypeArgument($parser);
            }
            $parser->match(Lexer::T_CLOSE_PARENTHESIS);
            $type .= '(' . \implode(', ', $parameters) . ')';
        }

        $this->parameters[self::TYPE_KEY] = $type;

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * Splits a target type into the type name and the arguments of its length or precision.
     *
     * @return array{0: string, 1: string[]}
     */
    public static function splitType(string $type): array
    {
        if (!\preg_match(self::TYPE_PATTERN, \strtolower(\trim($type)), $matches)) {
            throw new \InvalidArgumentException(\sprintf('Type %s is not a valid target type.', $type));
        }

        $arguments = isset($matches['arguments']) && '' !== $matches['arguments']
            ? \preg_split('/, ?/', $matches['arguments'])
            : [];

        return [$matches['type'], $arguments];
    }

    protected function isSupportedType(string $type): bool
    {
        return \in_array(\strtolower(\trim($type)), $this->supportedTypes, true);
    }

    /**
     * Matches a length or a precision argument of a target type.
     */
    protected function matchTypeArgument(Parser $parser): string
    {
        $lexer = $parser->getLexer();
        $parameter = $parser->Literal();
        $value = (string)$parameter->value;

        if (!\ctype_digit($value)) {
            $parser->syntaxError('Length or precision of a type to be a non-negative integer', $lexer->token);
        }

        return $value;
    }
}
