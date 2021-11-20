<?php

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\User;

use Symfony\Component\Uid\Uuid;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CreateCommandInterface;
use Zentlix\UserBundle\Domain\User\Entity\User;

class CreateCommand extends Command implements CreateCommandInterface
{
    public bool $sendRegistrationEmail = true;

    public function __construct()
    {
        $this->id         = Uuid::v4();
        $this->status     = User::STATUS_ACTIVE;
        $this->updated_at = new \DateTimeImmutable();
        $this->created_at = new \DateTimeImmutable();
    }
}
