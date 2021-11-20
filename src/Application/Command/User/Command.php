<?php

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\User;

use Symfony\Component\Validator\Constraints;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandInterface;
use Zentlix\MainBundle\Infrastructure\Share\Bus\EmailTrait;
use Zentlix\UserBundle\Domain\User\Entity\User;

class Command implements CommandInterface
{
    use EmailTrait;

    public $id;
    /**
     * @Constraints\NotBlank()
     * @Constraints\Email()
     */
    public ?string $email = null;
    public ?string $first_name = null;
    public ?string $last_name = null;
    public ?string $middle_name = null;
    public array $groups = [];
    public string|null $phone = null;
    public ?string $plain_password = null;
    /** @Constraints\NotBlank() */
    public ?string $status;
    public ?string $zip = null;
    public ?string $country = null;
    public ?string $city = null;
    public ?string $street = null;
    public ?string $house = null;
    public ?string $flat = null;
    public bool $email_confirmed = false;
    public \DateTimeImmutable $updated_at;
    public \DateTimeImmutable $created_at;
    public array $attributes = [];
    protected User $entity;

    public $user;

    public function getEntity(): User
    {
        return $this->entity;
    }
}
