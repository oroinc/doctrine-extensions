<?php

namespace Oro\ORM\Query\AST;

use Doctrine\Common\Inflector\Inflector as LegacyInflector;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Doctrine\ORM\Query\QueryException;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;

class FunctionFactory
{
    /**
     * @var Inflector
     */
    private static $inflector;

    /**
     * Create platform function node.
     *
     * @param string $platformName
     * @param string $functionName
     * @param array $parameters
     * @throws QueryException
     * @return PlatformFunctionNode
     */
    public static function create($platformName, $functionName, array $parameters)
    {
        $className = __NAMESPACE__
            . '\\Platform\\Functions\\'
            . self::classify(strtolower($platformName))
            . '\\'
            . self::classify(strtolower($functionName));

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
     * @param string $platformName
     * @return string
     */
    private static function classify($platformName)
    {
        if (class_exists(Inflector::class)) {
            if (!self::$inflector) {
                self::$inflector = InflectorFactory::create()->build();
            }
            return self::$inflector->classify($platformName);
        }
        return LegacyInflector::classify($platformName);
    }
}
