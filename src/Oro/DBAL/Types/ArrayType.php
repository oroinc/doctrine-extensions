<?php
declare(strict_types=1);

namespace Oro\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ArrayType as BaseType;

class ArrayType extends BaseType
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value && !$this->isSerialized($value)) {
            $value = \base64_decode($value);
        }

        return parent::convertToPHPValue($value, $platform);
    }

    /** @noinspection PhpMissingReturnTypeInspection */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $convertedValue = parent::convertToDatabaseValue($value, $platform);
        return \base64_encode($convertedValue);
    }

    protected function isSerialized(string $string): bool
    {
        return false !== \strpos($string, ';') || false !== \strpos($string, ':') || false !== \strpos($string, '{');
    }
}
