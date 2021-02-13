<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\User;

use Symfony\Component\Validator\Constraints;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as PhoneConstraint;
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
    /** @PhoneConstraint() */
    public ?PhoneNumber $phone = null;
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