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
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandInterface;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;

class Command implements CommandInterface
{
    public $id;

    /** @Constraints\NotBlank() */
    public ?string $title = null;

    public ?string $code = null;

    /** @Constraints\NotBlank() */
    public ?string $group_role = null;

    /** @Constraints\NotBlank() */
    public int $sort = 1;

    protected UserGroup $entity;

    public function getEntity(): UserGroup
    {
        return $this->entity;
    }
}