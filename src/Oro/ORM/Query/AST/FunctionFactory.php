<?php

namespace Oro\ORM\Query\AST;

use Doctrine\ORM\Query\QueryException;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;

class FunctionFactory
{
    /**
     * Create platform function node.
     *
     * @param string $platformName
     * @param string $functionName
     * @param array $parameters
     * @throws \Doctrine\ORM\Query\QueryException
     * @return PlatformFunctionNode
     */
    public static function create($platformName, $functionName, array $parameters)
    {
        $className = __NAMESPACE__
            . '\\Platform\\Functions\\'
            . self::classify($platformName)
            . '\\'
            . self::classify($functionName);

        if (!class_exists($className)) {
            throw QueryException::syntaxError(
                sprintf(
                    'Function "%s" does not supported for platform "%s"',
                    $functionName,
                    $platformName
                )
            );
        }

        return new $className($parameters);
    }

    /**
     * @param string $word
     * @return string
     */
    private static function classify($word)
    {
        return str_replace(array(' ', '_', '-'), '', ucwords(strtolower($word), ' _-'));
    }
}
