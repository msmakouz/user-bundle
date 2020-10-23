<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\User;

use Zentlix\MainBundle\Infrastructure\Share\Bus\CreateCommandInterface;
use Zentlix\UserBundle\Domain\User\Entity\User;

class CreateCommand extends Command implements CreateCommandInterface
{
    public bool $sendRegistrationEmail = true;

    public function __construct()
    {
        $this->status = User::STATUS_ACTIVE;

        $this->updated_at = new \DateTimeImmutable();
        $this->created_at = new \DateTimeImmutable();
    }
}