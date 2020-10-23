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

use Zentlix\MainBundle\Infrastructure\Share\Bus\UpdateCommandInterface;
use Zentlix\UserBundle\Domain\User\Entity\User;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;

class UpdateCommand extends Command implements UpdateCommandInterface
{
    public function __construct(User $user)
    {
        $this->entity          = $user;
        $this->email           = $user->getEmail()->getValue();
        $this->first_name      = $user->getFirstName();
        $this->last_name       = $user->getLastName();
        $this->status          = $user->getStatus();
        $this->middle_name     = $user->getMiddleName();
        $this->phone           = $user->getPhone();
        $this->zip             = $user->getZip();
        $this->country         = $user->getCountry();
        $this->city            = $user->getCity();
        $this->street          = $user->getStreet();
        $this->house           = $user->getHouse();
        $this->flat            = $user->getFlat();
        $this->email_confirmed = $user->isEmailConfirmed();
        $this->updated_at      = new \DateTimeImmutable();
        $this->created_at      = $user->getCreatedAt();

        /** @var UserGroup $group */
        foreach ($user->getGroups()->getValues() as $group) {
            $this->groups[$group->getCode()] = $group->getTitle();
        }
        $this->groups = array_flip($this->groups);
    }
}