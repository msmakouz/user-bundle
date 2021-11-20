<?php

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Group\Entity;

use Doctrine\ORM\Mapping;
use Gedmo\Mapping\Annotation\Slug;
use Zentlix\MainBundle\Domain\Shared\Entity\Eventable;
use Zentlix\MainBundle\Domain\Shared\Entity\SortTrait;
use Zentlix\UserBundle\Application\Command\Group\CreateCommand;
use Zentlix\UserBundle\Application\Command\Group\UpdateCommand;

/**
 * @Mapping\Entity(repositoryClass="Zentlix\UserBundle\Domain\Group\Repository\GroupRepository")
 * @Mapping\Table(name="zentlix_user_groups", uniqueConstraints={
 *     @Mapping\UniqueConstraint(columns={"code"})
 * })
 */
class UserGroup implements Eventable
{
    use SortTrait;

    const GROUP_ROLE_USER = 'ROLE_USER';
    const GROUP_ROLE_ADMIN = 'ROLE_ADMIN';
    const USER_GROUP = 'user-group';
    const ADMIN_GROUP = 'admin-group';

    /**
     * @Mapping\Id
     * @Mapping\Column(type="uuid", unique=true)
     */
    private $id;

    /** @Mapping\Column(type="string", length=255) */
    private $title;

    /**
     * @Slug(fields={"title"}, updatable=false)
     * @Mapping\Column(type="string", length=64, unique=true)
     */
    private $code;

    /** @Mapping\Column(type="string") */
    private $group_role;

    /** @Mapping\Column(type="json") */
    private $rights = [];

    private $systemGroups = [self::USER_GROUP, self::ADMIN_GROUP];

    public function __construct(CreateCommand $command)
    {
        $this->id = $command->id;

        $this->setValuesFromCommands($command);
    }

    public function update(UpdateCommand $command)
    {
        $this->setValuesFromCommands($command);

        if($this->isRoleAdmin()) {
            $this->rights = $command->rights;
        }
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getGroupRole(): string
    {
        return $this->group_role;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getRights(): array
    {
        return $this->rights;
    }

    public function isCodeEqual(string $code): bool
    {
        return $code === $this->code;
    }

    public function isAccessGranted(string $command): bool
    {
        if($this->code === self::ADMIN_GROUP) {
            return true;
        }

        return $this->rights[$command] ?? false;
    }

    public function isRoleAdmin(): bool
    {
        return $this->group_role === self::GROUP_ROLE_ADMIN;
    }

    public function isSystemGroup($group): bool
    {
        return in_array($group, $this->systemGroups);
    }

    public function isAdminGroup(): bool
    {
        return $this->code === self::ADMIN_GROUP;
    }

    /**
     * @param CreateCommand|UpdateCommand $command $command
     */
    private function setValuesFromCommands($command): void
    {
        $this->title      = $command->title;
        $this->code       = $command->code;
        $this->group_role = $command->group_role;
        $this->sort       = $command->sort;
    }
}
