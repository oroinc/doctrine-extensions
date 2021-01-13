<?php
declare(strict_types=1);

namespace Oro\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class MoneyType extends Type
{
    public const TYPE = 'money';
    public const TYPE_PRECISION = 19;
    public const TYPE_SCALE = 4;

    /**
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function getName()
    {
        return self::TYPE;
    }

    /**
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        $column['precision'] = self::TYPE_PRECISION;
        $column['scale']     = self::TYPE_SCALE;

        return $platform->getDecimalTypeDeclarationSQL($column);
    }

    /**
     * @noinspection SenselessMethodDuplicationInspection
     * @noinspection PhpMissingParentCallCommonInspection
     * @noinspection PhpDocSignatureInspection
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    /**
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection PhpMissingParentCallCommonInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
