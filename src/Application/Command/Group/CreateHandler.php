<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\Group;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandHandlerInterface;
use Zentlix\UserBundle\Domain\Group\Event\BeforeCreate;
use Zentlix\UserBundle\Domain\Group\Event\AfterCreate;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;
use Zentlix\UserBundle\Domain\Group\Specification\UniqueCodeSpecification;

class CreateHandler implements CommandHandlerInterface
{
    private UniqueCodeSpecification $uniqueCodeSpecification;
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(UniqueCodeSpecification $uniqueCodeSpecification,
                                EntityManagerInterface $entityManager,
                                EventDispatcherInterface $eventDispatcher)
    {
        $this->uniqueCodeSpecification = $uniqueCodeSpecification;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(CreateCommand $command): void
    {
        if($command->code) {
            $this->uniqueCodeSpecification->isUnique($command->code);
        }

        $this->eventDispatcher->dispatch(new BeforeCreate($command));

        $group = new UserGroup($command);

        $this->entityManager->persist($group);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new AfterCreate($group, $command));
    }
}