<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Functions\Numeric;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\Lexer;
use Oro\ORM\Query\AST\Functions\AbstractPlatformAwareFunctionNode;

class TimestampDiff extends AbstractPlatformAwareFunctionNode
{
    public const UNIT_KEY = 'unit';
    public const VAL1_KEY = 'val1';
    public const VAL2_KEY = 'val2';

    /**
     * @var array
     */
    protected $supportedUnits = [
        'MICROSECOND',
        'SECOND',
        'MINUTE',
        'HOUR',
        'DAY',
        'WEEK',
        'MONTH',
        'QUARTER',
        'YEAR'
    ];

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $parser->match(Lexer::T_IDENTIFIER);

        $lexer = $parser->getLexer();
        $unit = strtoupper(trim($lexer->token['value']));
        if (!$this->isSupportedUnit($unit)) {
            $parser->syntaxError(
                \sprintf(
                    'Unit %s is not supported by TIMESTAMPDIFF function. The supported units are: "%s"',
                    $unit,
                    \implode(', ', $this->supportedUnits)
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

    protected function isSupportedUnit(string $unit): bool
    {
        return \in_array($unit, $this->supportedUnits, false);
    }
}
