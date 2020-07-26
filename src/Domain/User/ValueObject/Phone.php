<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\User\ValueObject;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

class Phone extends PhoneNumber
{
    /**
     * @return string
     */
    public function getValue()
    {
        return $this->getFormatted(PhoneNumberFormat::E164);
    }

    /**
     * @param int $format
     * @return string
     */
    public function getFormatted($format = PhoneNumberFormat::INTERNATIONAL)
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        return $phoneNumberUtil->format($this, $format);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getFormatted();
    }
}