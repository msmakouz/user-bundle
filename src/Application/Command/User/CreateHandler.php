<?php

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zentlix\MainBundle\Domain\Attribute\Service\Attributes;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandHandlerInterface;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;
use Zentlix\UserBundle\Domain\Group\Repository\GroupRepository;
use Zentlix\UserBundle\Domain\Group\Specification\ExistGroupByCodeSpecification;
use Zentlix\UserBundle\Domain\Mailer\MailEvent\UserRegistration;
use Zentlix\UserBundle\Domain\User\Event\BeforeCreate;
use Zentlix\UserBundle\Domain\User\Event\AfterCreate;
use Zentlix\UserBundle\Domain\User\Entity\User;
use Zentlix\UserBundle\Domain\User\Specification\UniqueEmailSpecification;
use Zentlix\UserBundle\Infrastructure\Mailer\Service\MailerInterface;

class CreateHandler implements CommandHandlerInterface
{
    public function __construct(
        private UniqueEmailSpecification $uniqueEmailSpecification,
        private ExistGroupByCodeSpecification $existGroupByCodeSpecification,
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
        private MailerInterface $mailer,
        private UserPasswordHasherInterface $passwordHasher,
        private GroupRepository $groupRepository,
        private Attributes $attributes
    ) {
    }

    public function __invoke(CreateCommand $command): void
    {
        $this->validate($command);

        $command->groups = $this->groupRepository->findByCode($command->groups);

        $this->eventDispatcher->dispatch(new BeforeCreate($command));

        $user = new User($command);
        $user->setPassword($this->passwordHasher->hashPassword($user, $command->plain_password));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->attributes->saveValues($user, $command->attributes);

        if($command->sendRegistrationEmail) {
            $this->mailer->send(UserRegistration::class, $user->getEmail()->getValue(), ['user' => $user]);
        }

        $this->eventDispatcher->dispatch(new AfterCreate($user, $command));

        $command->user = $user;
    }

    private function validate(CreateCommand $command): void
    {
        $this->uniqueEmailSpecification->isUnique($command->getEmailObject());
        if(\count($command->groups) === 0) {
            $command->groups = [UserGroup::USER_GROUP];
        }

        foreach ($command->groups as $group) {
            $this->existGroupByCodeSpecification->isExist($group);
        }
    }
}
