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
use Zentlix\MainBundle\Infrastructure\Share\Bus\UpdateCommandInterface;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;

class UpdateCommand extends Command implements UpdateCommandInterface
{
    /** @Constraints\NotBlank() */
    public ?string $code = null;
    public array $rights = [];

    public function __construct(UserGroup $group)
    {
        $this->title      = $group->getTitle();
        $this->code       = $group->getCode();
        $this->group_role = $group->getGroupRole();
        $this->sort       = $group->getSort();
        $this->entity     = $group;

        foreach ($group->getRights() as $class => $isVisible) {
            $this->__set($class, $isVisible);
        }
    }

    public function getEntity(): UserGroup
    {
        return $this->entity;
    }

    public function __get($right): bool
    {
        if($this->__isset($right)) {
            return $this->rights[(string) str_replace(':', '\\', $right)];
        }

        return false;
    }

    public function __set($right, $isGranted): void
    {
        $this->rights[(string) str_replace(':', '\\', $right)] = $isGranted;
    }

    public function __isset($right): bool
    {
        return isset($this->rights[(string) str_replace(':', '\\', $right)]);
    }
}