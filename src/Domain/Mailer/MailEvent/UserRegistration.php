<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Mailer\MailEvent;

use Zentlix\UserBundle\Infrastructure\Mailer\Event\EventInterface;

class UserRegistration implements EventInterface
{
    public function getTitle(): string
    {
        return 'zentlix_user.register_user';
    }

    public function getAvailableVariables(): array
    {
        return [
            'user.email'       => 'zentlix_user.user.email',
            'user.first_name'  => 'zentlix_user.first_name',
            'user.last_name'   => 'zentlix_user.last_name',
            'user.middle_name' => 'zentlix_user.middle_name',
            'user.phone'       => 'zentlix_user.phone_number',
            'user.zip'         => 'zentlix_user.zip',
            'user.country'     => 'zentlix_user.country',
            'user.city'        => 'zentlix_user.city',
            'user.street'      => 'zentlix_user.street',
            'user.house'       => 'zentlix_user.house',
            'user.flat'        => 'zentlix_user.flat'
        ];
    }
}