<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\User\Event\User;

use Zentlix\UserBundle\Domain\User\ValueObject\Email;

final class BeforeLogin
{
    /**
     * @var Email
     */
    private $email;
    private $password;

    public function __construct(Email $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}