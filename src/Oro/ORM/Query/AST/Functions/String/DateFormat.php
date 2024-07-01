<?php
declare(strict_types=1);

namespace Oro\ORM\Query\AST\Functions\String;

use Doctrine\ORM\Query\TokenType;
use Doctrine\ORM\Query\Parser;
use Oro\ORM\Query\AST\Functions\AbstractPlatformAwareFunctionNode;

class DateFormat extends AbstractPlatformAwareFunctionNode
{
    public const DATE_KEY = 'date';
    public const FORMAT_KEY = 'format';

    /** @var array */
    private static array $knownFormats = [
        '%a',
        '%b',
        '%c',
        '%D',
        '%d',
        '%e',
        '%f',
        '%H',
        '%h',
        '%I',
        '%i',
        '%j',
        '%k',
        '%l',
        '%M',
        '%m',
        '%p',
        '%r',
        '%S',
        '%s',
        '%T',
        '%U',
        '%u',
        '%V',
        '%v',
        '%W',
        '%w',
        '%X',
        '%x',
        '%Y',
        '%y',
        '%%',
    ];

    /** @var array */
    private static array $supportedFormats = [
        '%a',
        '%b',
        '%c',
        '%d',
        '%e',
        '%f',
        '%H',
        '%h',
        '%I',
        '%i',
        '%j',
        '%k',
        '%l',
        '%M',
        '%m',
        '%p',
        '%r',
        '%S',
        '%s',
        '%T',
        '%W',
        '%Y',
        '%y',
        '%%',
    ];

    /**
     * {@inheritdoc}
     */
    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);

        $this->parameters[self::DATE_KEY] = $parser->ArithmeticPrimary();

        $parser->match(TokenType::T_COMMA);

        $this->parameters[self::FORMAT_KEY] = $parser->StringPrimary();
        $this->validateFormat($parser);

        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }

    private function validateFormat(Parser $parser): void
    {
        $format = \str_replace('%%', '', (string)$this->parameters[self::FORMAT_KEY]);
        $unsupportedFormats = \array_diff(self::$knownFormats, self::$supportedFormats);
        foreach ($unsupportedFormats as $unsupportedFormat) {
            if (str_contains($format, $unsupportedFormat)) {
                $parser->syntaxError(
                    \sprintf(
                        'Format string contains unsupported specifier %s. The supported specifiers are: "%s"',
                        $unsupportedFormat,
                        \implode(', ', self::$supportedFormats)
                    )
                );
                break;
            }
        }
    }
}
