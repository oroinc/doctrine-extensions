<?php

namespace Oro\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ObjectType as BaseType;

class ObjectType extends BaseType
{
    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value && strpos($value, ';') === false) {
            $value = base64_decode($value);
        }

        return parent::convertToPHPValue($value, $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $convertedValue = parent::convertToDatabaseValue($value, $platform);
        return base64_encode($convertedValue);
    }
}
