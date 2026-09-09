<?php

namespace Oro\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;

class Cast extends AbstractPlatformAwareFunctionNode
{
    const PARAMETER_KEY = 'expression';
    const TYPE_KEY = 'type';

    /**
     * A target type: a type name, optionally followed by a length or a precision.
     */
    const TYPE_PATTERN = '/^(?P<type>[a-z]+)(?:\((?P<arguments>\d+(?:, ?\d+)*)\))?$/';

    protected $supportedTypes = array(
        'char',
        'string',
        'text',
        'date',
        'datetime',
        'time',
        'int',
        'integer',
        'decimal',
        'json',
        'bool',
        'boolean'
    );

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->parameters[self::PARAMETER_KEY] = $parser->ArithmeticExpression();

        $parser->match(Lexer::T_AS);

        $parser->match(Lexer::T_IDENTIFIER);
        $lexer = $parser->getLexer();
        $type = $lexer->token['value'];

        if (!$this->checkType($type)) {
            $parser->syntaxError(
                sprintf(
                    'Type unsupported. Supported types are: "%s"',
                    implode(', ', $this->supportedTypes)
                ),
                $lexer->token
            );
        }

        if ($lexer->isNextToken(Lexer::T_OPEN_PARENTHESIS)) {
            $parser->match(Lexer::T_OPEN_PARENTHESIS);
            $parameters = array(
                $this->matchTypeArgument($parser)
            );
            while ($lexer->isNextToken(Lexer::T_COMMA)) {
                $parser->match(Lexer::T_COMMA);
                $parameters[] = $this->matchTypeArgument($parser);
            }
            $parser->match(Lexer::T_CLOSE_PARENTHESIS);
            $type .= '(' . implode(', ', $parameters) . ')';
        }

        $this->parameters[self::TYPE_KEY] = $type;

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * Split a target type into the type name and the arguments of its length or precision.
     *
     * @param string $type
     * @return array
     * @throws \InvalidArgumentException
     */
    public static function splitType($type)
    {
        if (!preg_match(self::TYPE_PATTERN, strtolower(trim($type)), $matches)) {
            throw new \InvalidArgumentException(sprintf('Type %s is not a valid target type.', $type));
        }

        $arguments = isset($matches['arguments']) && '' !== $matches['arguments']
            ? preg_split('/, ?/', $matches['arguments'])
            : array();

        return array($matches['type'], $arguments);
    }

    /**
     * Check that given type is supported.
     *
     * @param string $type
     * @return bool
     */
    protected function checkType($type)
    {
        return in_array(strtolower(trim($type)), $this->supportedTypes, true);
    }

    /**
     * Match a length or a precision argument of a target type.
     *
     * @param Parser $parser
     * @return string
     */
    protected function matchTypeArgument(Parser $parser)
    {
        $lexer = $parser->getLexer();
        /** @var Literal $parameter */
        $parameter = $parser->Literal();
        $value = (string)$parameter->value;

        if (!ctype_digit($value)) {
            $parser->syntaxError('Length or precision of a type to be a non-negative integer', $lexer->token);
        }

        return $value;
    }
}
