<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Infrastructure\Share\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Misd\PhoneNumberBundle\Doctrine\DBAL\Types\PhoneNumberType;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\NumberParseException;
use Zentlix\UserBundle\Domain\User\ValueObject\Phone;

class PhoneType extends PhoneNumberType
{
    const NAME = 'phone';

    public function getName()
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value || $value instanceof Phone) {
            return $value;
        }

        $util = PhoneNumberUtil::getInstance();

        try {
            return $util->parse($value, PhoneNumberUtil::UNKNOWN_REGION, new Phone());
        } catch (NumberParseException $e) {
            throw ConversionException::conversionFailed($value, self::NAME);
        }
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}