<?php
declare(strict_types=1);

namespace Oro\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ObjectType as BaseType;

class ObjectType extends BaseType
{
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value && !$this->isSerialized((string) $value)) {
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
