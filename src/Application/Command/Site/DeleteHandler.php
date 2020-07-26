<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\Site;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zentlix\MainBundle\Application\Command\CommandHandlerInterface;
use Zentlix\UserBundle\Domain\User\Event\Site\AfterDelete;
use Zentlix\UserBundle\Domain\User\Event\Site\BeforeDelete;

class DeleteHandler implements CommandHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $entityManager,
                                EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(DeleteCommand $command): void
    {
        $siteId = $command->site->getId();

        $this->eventDispatcher->dispatch(new BeforeDelete($command));

        $this->entityManager->remove($command->site);

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new AfterDelete($siteId));
    }
}