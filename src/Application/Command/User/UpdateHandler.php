<?php

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zentlix\MainBundle\Domain\Attribute\Service\Attributes;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandHandlerInterface;
use Zentlix\UserBundle\Domain\Admin\Repository\SettingRepository;
use Zentlix\UserBundle\Domain\Group\Specification\ExistGroupByCodeSpecification;
use Zentlix\UserBundle\Domain\Group\Repository\GroupRepository;
use Zentlix\UserBundle\Domain\User\Entity\User;
use Zentlix\UserBundle\Domain\User\Event\BeforeUpdate;
use Zentlix\UserBundle\Domain\User\Event\AfterUpdate;
use Zentlix\UserBundle\Domain\User\Specification\UniqueEmailSpecification;

class UpdateHandler implements CommandHandlerInterface
{
    public function __construct(
        private UniqueEmailSpecification $uniqueEmailSpecification,
        private ExistGroupByCodeSpecification $existGroupByCodeSpecification,
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
        private UserPasswordHasherInterface $passwordEncoder,
        private GroupRepository $groupRepository,
        private SettingRepository $settingRepository,
        private Attributes $attributes
    ) {
    }

    public function __invoke(UpdateCommand $command): void
    {
        $user = $command->getEntity();

        $this->validate($command, $user);

        $command->groups = $this->groupRepository->findByCode($command->groups);

        $this->eventDispatcher->dispatch(new BeforeUpdate($command));

        $this->attributes->saveValues($user, $command->attributes);

        $user->update($command);
        if($command->plain_password) {
            $user->setPassword($this->passwordEncoder->hashPassword($user, $command->plain_password));
        }
        $this->deleteAdminSettings($user);

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new AfterUpdate($user, $command));
    }

    private function deleteAdminSettings(User $user): void
    {
        if($user->isAdminRole() === false) {
            $adminSetting = $this->settingRepository->findByUserId($user->getId());

            if(is_null($adminSetting) === false) {
                $this->entityManager->remove($adminSetting);
            }
        }
    }

    private function validate(UpdateCommand $command, User $user): void
    {
        if(!$user->getEmail()->isEqual($command->getEmailObject())) {
            $this->uniqueEmailSpecification->isUnique($command->getEmailObject());
        }
        foreach ($command->groups as $group) {
            $this->existGroupByCodeSpecification->isExist($group);
        }
    }
}
