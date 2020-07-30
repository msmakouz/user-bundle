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

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zentlix\MainBundle\Application\Command\CommandHandlerInterface;
use Zentlix\UserBundle\Domain\User\Event\User\BeforeDelete;
use Zentlix\UserBundle\Domain\User\Event\User\AfterDelete;
use Zentlix\UserBundle\Domain\Admin\Repository\SettingRepository;
use Zentlix\MainBundle\Domain\DataTable\Repository\DataTableRepository;

class DeleteHandler implements CommandHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;
    private SettingRepository $settingRepository;
    private DataTableRepository $dataTableRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                EventDispatcherInterface $eventDispatcher,
                                SettingRepository $settingRepository,
                                DataTableRepository $dataTableRepository)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->settingRepository = $settingRepository;
        $this->dataTableRepository = $dataTableRepository;
    }

    public function __invoke(DeleteCommand $command): void
    {
        $userId = $command->user->getId();

        $this->eventDispatcher->dispatch(new BeforeDelete($command));

        $adminSetting = $this->settingRepository->findByUserId($userId);

        if(is_null($adminSetting) === false) {
            $this->entityManager->remove($adminSetting);
        }

        $dataTables = $this->dataTableRepository->findByUserId($userId);
        foreach ($dataTables as $dataTable) {
            $this->entityManager->remove($dataTable);
        }

        $this->entityManager->remove($command->user);

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new AfterDelete($userId));
    }
}