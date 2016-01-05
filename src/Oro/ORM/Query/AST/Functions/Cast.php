<?php

namespace Oro\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;

class Cast extends AbstractPlatformAwareFunctionNode
{
    const PARAMETER_KEY = 'expression';
    const TYPE_KEY = 'type';

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

        if ($lexer->isNextToken(Lexer::T_OPEN_PARENTHESIS)) {
            $parser->match(Lexer::T_OPEN_PARENTHESIS);
            /** @var Literal $parameter */
            $parameter = $parser->Literal();
            $parameters = array(
                $parameter->value
            );
            if ($lexer->isNextToken(Lexer::T_COMMA)) {
                while ($lexer->isNextToken(Lexer::T_COMMA)) {
                    $parser->match(Lexer::T_COMMA);
                    $parameter = $parser->Literal();
                    $parameters[] = $parameter->value;
                }
            }
            $parser->match(Lexer::T_CLOSE_PARENTHESIS);
            $type .= '(' . implode(', ', $parameters) . ')';
        }

        if (!$this->checkType($type)) {
            $parser->syntaxError(
                sprintf(
                    'Type unsupported. Supported types are: "%s"',
                    implode(', ', $this->supportedTypes)
                ),
                $lexer->token
            );
        }

        $this->parameters[self::TYPE_KEY] = $type;

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * Check that given type is supported.
     *
     * @param string $type
     * @return bool
     */
    protected function checkType($type)
    {
        $type = strtolower(trim($type));
        foreach ($this->supportedTypes as $supportedType) {
            if (strpos($type, $supportedType) === 0) {
                return true;
            }
        }

        return false;
    }
}
