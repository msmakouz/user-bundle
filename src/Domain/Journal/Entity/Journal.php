<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Journal\Entity;

use Doctrine\ORM\Mapping;
use Zentlix\UserBundle\Domain\User\ValueObject\Email;
use Zentlix\MainBundle\Infrastructure\Share\Doctrine\Uuid;
use Zentlix\MainBundle\Infrastructure\Share\Doctrine\UuidInterface;

/**
 * @Mapping\Entity(repositoryClass="Zentlix\UserBundle\Domain\Journal\Repository\JournalRepository")
 * @Mapping\Table(name="zentlix_user_auth_journal")
 */
class Journal
{
    /**
     * @var UuidInterface
     * @Mapping\Id
     * @Mapping\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @var Email
     * @Mapping\Column(type="email", length=180)
     */
    private $email;

    /** @Mapping\Column(type="string", length=64) */
    private $ip;

    /**
     * @var \DateTimeImmutable
     * @Mapping\Column(type="datetime_immutable")
     */
    private $date;

    /** @Mapping\Column(type="boolean") */
    private $success;

    /** @Mapping\Column(type="string", length=64, nullable=true) */
    private $reason;

    public function __construct(string $email, string $ip, bool $success = true, string $reason = null)
    {
        $this->id = Uuid::uuid4();
        $this->email = new Email($email);
        $this->ip = $ip;
        $this->success = $success;
        $this->reason = $reason;
        $this->date = new \DateTimeImmutable();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }
}