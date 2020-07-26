<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Admin\Entity;

use Doctrine\ORM\Mapping;
use Zentlix\MainBundle\Infrastructure\Share\Doctrine\Uuid;
use Zentlix\MainBundle\Infrastructure\Share\Doctrine\UuidInterface;
use Zentlix\UserBundle\Domain\User\Entity\User;

/**
 * @Mapping\Entity(repositoryClass="Zentlix\UserBundle\Domain\Admin\Repository\NotificationRepository")
 * @Mapping\Table(name="zx_admin_notifications")
 */
class Notification
{
    /**
     * @var UuidInterface
     * @Mapping\Id
     * @Mapping\Column(type="uuid_binary")
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
        $this->id = Uuid::uuid4();
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