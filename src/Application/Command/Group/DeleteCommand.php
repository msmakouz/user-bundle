<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\Group;

use Symfony\Component\Validator\Constraints;
use Zentlix\MainBundle\Application\Command\DeleteCommandInterface;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandInterface;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;

class DeleteCommand implements CommandInterface, DeleteCommandInterface
{
    /** @Constraints\NotBlank() */
    public UserGroup $group;

    public function __construct(UserGroup $group)
    {
        $this->group = $group;
    }
}