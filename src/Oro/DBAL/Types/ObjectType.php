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
        if ($value && !$this->isSerialized($value)) {
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

    /**
     * @param string $string
     * @return bool
     */
    protected function isSerialized($string)
    {
        return strpos($string, ';') !== false || strpos($string, ':') !== false || strpos($string, '{') !== false;
    }
}
