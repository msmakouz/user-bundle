<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\AdminSetting\Locale;

use Symfony\Component\Validator\Constraints;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandInterface;

class ChangeLocaleCommand implements CommandInterface
{
    /** @Constraints\NotBlank() */
    public int $locale_id;

    public function __construct(int $locale_id)
    {
        $this->locale_id = $locale_id;
    }
}