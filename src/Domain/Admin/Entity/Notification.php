<?php

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Admin\Entity;

use Doctrine\ORM\Mapping;
use Symfony\Component\Uid\Uuid;
use Zentlix\UserBundle\Domain\User\Entity\User;

/**
 * @Mapping\Entity(repositoryClass="Zentlix\UserBundle\Domain\Admin\Repository\NotificationRepository")
 * @Mapping\Table(name="zentlix_user_admin_notifications")
 */
class Notification
{
    /**
     * @Mapping\Id
     * @Mapping\Column(type="uuid", unique=true)
     */
    private $id;

    /** @Mapping\Column(type="string", length=64) */
    private $event;

    /** @Mapping\Column(type="string", length=255) */
    private $href;

    /** @Mapping\Column(type="string", length=255) */
    private $icon;

    /**
     * @var \DateTimeImmutable
     * @Mapping\Column(type="datetime_immutable")
     */
    private $date;

    /**
     * @var User
     * @Mapping\ManyToOne(targetEntity="Zentlix\UserBundle\Domain\User\Entity\User")
     * @Mapping\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function __construct(string $event, string $href, string $icon, User $user)
    {
        $this->id = Uuid::v4();
        $this->event = $event;
        $this->href = $href;
        $this->icon = $icon;
        $this->user = $user;
        $this->date = new \DateTimeImmutable();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
