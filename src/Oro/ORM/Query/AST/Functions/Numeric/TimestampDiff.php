<?php

namespace Oro\ORM\Query\AST\Functions\Numeric;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;
use Oro\ORM\Query\AST\Functions\AbstractPlatformAwareFunctionNode;

class TimestampDiff extends AbstractPlatformAwareFunctionNode
{
    const UNIT_KEY = 'unit';
    const VAL1_KEY = 'val1';
    const VAL2_KEY = 'val2';

    /**
     * List of supported units.
     *
     * @var array
     */
    protected $supportedUnits = array(
        'MICROSECOND',
        'SECOND',
        'MINUTE',
        'HOUR',
        'DAY',
        'WEEK',
        'MONTH',
        'QUARTER',
        'YEAR'
    );

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_IDENTIFIER);

        $lexer = $parser->getLexer();
        $unit = strtoupper(trim($lexer->token['value']));
        if (!$this->checkUnit($unit)) {
            $parser->syntaxError(
                sprintf(
                    'Unit is not valid for TIMESTAMPDIFF function. Supported units are: "%s"',
                    implode(', ', $this->supportedUnits)
                ),
                $lexer->token
            );
        }

        $this->parameters[self::UNIT_KEY] = $unit;
        $parser->match(Lexer::T_COMMA);
        $this->parameters[self::VAL1_KEY] = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->parameters[self::VAL2_KEY] = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    /**
     * Check that unit is supported.
     *
     * @param string $unit
     * @return bool
     */
    protected function checkUnit($unit)
    {
        return in_array($unit, $this->supportedUnits);
    }
}
