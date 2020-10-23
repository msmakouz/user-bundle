<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\AdminSetting\Widgets;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandHandlerInterface;
use Zentlix\UserBundle\Domain\Admin\Event\Setting\BeforeChangeWidgets;
use Zentlix\UserBundle\Domain\Admin\Event\Setting\AfterChangeWidgets;
use Zentlix\UserBundle\Domain\Admin\Service\AdminSettings;
use Zentlix\UserBundle\Domain\Admin\Specification\ExistWidgetSpecification;

class ChangeWidgetsHandler implements CommandHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;
    private AdminSettings $adminSettings;
    private ExistWidgetSpecification $existWidgetSpecification;

    public function __construct(AdminSettings $adminSettings,
                                EntityManagerInterface $entityManager,
                                EventDispatcherInterface $eventDispatcher,
                                ExistWidgetSpecification $existWidgetSpecification)
    {
        $this->adminSettings = $adminSettings;
        $this->existWidgetSpecification = $existWidgetSpecification;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(ChangeWidgetsCommand $command): void
    {
        foreach (get_object_vars($command) as $widget => $isVisible) {
            $this->existWidgetSpecification->isExist($widget);
        }

        $this->eventDispatcher->dispatch(new BeforeChangeWidgets($command));

        $settings = $this->adminSettings->getSettings();
        $settings->setWidgets(get_object_vars($command));

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new AfterChangeWidgets($settings->getWidgets()));
    }
}