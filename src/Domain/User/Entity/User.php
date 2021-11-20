<?php

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\User\Entity;

use Doctrine\ORM\Mapping;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Zentlix\MainBundle\Domain\Shared\Entity\Eventable;
use Zentlix\MainBundle\Infrastructure\Attribute\Entity\SupportAttributeInterface;
use Zentlix\UserBundle\Domain\User\ValueObject\Email;
use Zentlix\UserBundle\Application\Command\User\CreateCommand;
use Zentlix\UserBundle\Application\Command\User\UpdateCommand;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;

/**
 * @Mapping\Entity(repositoryClass="Zentlix\UserBundle\Domain\User\Repository\UserRepository")
 * @Mapping\Table(name="zentlix_user_users", uniqueConstraints={
 *     @Mapping\UniqueConstraint(columns={"email"})
 * })
 */
class User implements UserInterface, Eventable, SupportAttributeInterface, PasswordAuthenticatedUserInterface
{
    const STATUS_BLOCKED = 'blocked';
    const STATUS_ACTIVE = 'active';
    const STATUS_WAIT = 'wait';

    /**
     * @Mapping\Id
     * @Mapping\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @var Email
     * @Mapping\Column(type="email", length=180, unique=true)
     */
    private $email;

    /** @Mapping\Column(type="string", length=255, nullable=true) */
    private $first_name;

    /** @Mapping\Column(type="string", length=255, nullable=true) */
    private $last_name;

    /** @Mapping\Column(type="string", length=255, nullable=true) */
    private $middle_name;

    /**
     * @var string
     * @Mapping\Column(type="string", length=35, nullable=true)
     */
    private $phone;

    /** @Mapping\Column(type="string", length=32, nullable=true) */
    private $zip;

    /** @Mapping\Column(type="string", length=255, nullable=true) */
    private $country;

    /** @Mapping\Column(type="string", length=255, nullable=true) */
    private $city;

    /** @Mapping\Column(type="string", length=255, nullable=true) */
    private $street;

    /** @Mapping\Column(type="string", length=255, nullable=true) */
    private $house;

    /** @Mapping\Column(type="string", length=255, nullable=true) */
    private $flat;

    /** @Mapping\Column(type="boolean") */
    private $email_confirmed;

    /** @Mapping\Column(type="string", length=255, nullable=true) */
    private $email_confirm_token;

    /**
     * @Mapping\ManyToMany(targetEntity="Zentlix\UserBundle\Domain\Group\Entity\UserGroup")
     * @Mapping\JoinTable(name="zentlix_user_user_groups",
     *     inverseJoinColumns={@Mapping\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    private $groups;

    /** @Mapping\Column(type="string", length=64, options={"default": "active"}) */
    private $status;

    /**
     * @var string The hashed password
     * @Mapping\Column(type="string")
     */
    private $password;

    /**
     * @var ResetToken|null
     * @Mapping\Embedded(class="ResetToken", columnPrefix="reset_token_")
     */
    private $resetToken;

    /**
     * @var Email|null
     * @Mapping\Column(type="email", name="new_email", nullable=true)
     */
    private $newEmail;

    /**
     * @var string|null
     * @Mapping\Column(type="string", name="new_email_token", nullable=true)
     */
    private $newEmailToken;

    /**
     * @var \DateTimeImmutable
     * @Mapping\Column(type="datetime_immutable", nullable=true)
     */
    private $last_login;

    /**
     * @var \DateTimeImmutable
     * @Mapping\Column(type="datetime_immutable")
     */
    private $updated_at;

    /**
     * @var \DateTimeImmutable
     * @Mapping\Column(type="datetime_immutable")
     */
    private $created_at;

    public function __construct(CreateCommand $command)
    {
        $this->id = $command->id;

        $this->setValuesFromCommands($command);
    }

    public function update(UpdateCommand $command): void
    {
        $this->setValuesFromCommands($command);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function getMiddleName(): ?string
    {
        return $this->middle_name;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getZip(): ?string
    {
        return $this->zip;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getHouse(): ?string
    {
        return $this->house;
    }

    public function getFlat()
    {
        return $this->flat;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = [];
        /** @var UserGroup $group */
        foreach ($this->groups->getValues() as $group) {
            $roles[] = $group->getGroupRole();
        }

        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function getLastLogin(): ?\DateTimeImmutable
    {
        return $this->last_login;
    }

    public function setLastLogin(\DateTimeImmutable $lastLogin): User
    {
        $this->last_login = $lastLogin;

        return $this;
    }

    public function getSalt() {}

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function confirmEmail(): User
    {
        $this->email_confirmed = true;
        $this->email_confirm_token = null;

        return $this;
    }

    public function isAdminRole(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }

    public function isAdminGroup(): bool
    {
        /** @var UserGroup $group */
        foreach ($this->groups->getValues() as $group) {
            if($group->isAdminGroup()) {
                return true;
            }
        }

        return false;
    }

    public function isEmailConfirmed(): bool
    {
        return (boolean) $this->email_confirmed;
    }

    public function isBlocked()
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isWait()
    {
        return $this->status === self::STATUS_WAIT;
    }

    public function isAccessGranted(string $command): bool
    {
        if(!$this->isAdminRole() || !$this->isActive()) {
            return false;
        }

        /** @var UserGroup $group $group */
        foreach ($this->groups->getValues() as $group) {
            if($group->isAccessGranted($command)) {
                return true;
            }
        }

        return false;
    }

    public static function getEntityTitle(): string
    {
        return 'zentlix_user.user.user';
    }

    public static function getEntityCode(): string
    {
        return 'user';
    }

    /**
     * @param CreateCommand|UpdateCommand $command $command
     */
    private function setValuesFromCommands($command): void
    {
        $this->email           = $command->getEmailObject();
        $this->status          = $command->status;
        $this->first_name      = $command->first_name;
        $this->last_name       = $command->last_name;
        $this->middle_name     = $command->middle_name;
        $this->phone           = $command->phone;
        $this->zip             = $command->zip;
        $this->country         = $command->country;
        $this->city            = $command->city;
        $this->street          = $command->street;
        $this->house           = $command->house;
        $this->flat            = $command->flat;
        $this->email_confirmed = $command->email_confirmed;
        $this->created_at      = $command->created_at;
        $this->updated_at      = $command->updated_at;
        $this->groups          = new ArrayCollection($command->groups);
    }

    public function getUserIdentifier(): string
    {
        return $this->email->getValue();
    }
}