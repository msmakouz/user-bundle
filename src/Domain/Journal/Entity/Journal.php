<?php

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Journal\Entity;

use Doctrine\ORM\Mapping;
use Symfony\Component\Uid\Uuid;
use Zentlix\UserBundle\Domain\User\ValueObject\Email;

/**
 * @Mapping\Entity(repositoryClass="Zentlix\UserBundle\Domain\Journal\Repository\JournalRepository")
 * @Mapping\Table(name="zentlix_user_auth_journal")
 */
class Journal
{
    /**
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
        $this->id = Uuid::v4();
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
