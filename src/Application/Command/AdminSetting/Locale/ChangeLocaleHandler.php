<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\AdminSetting\Locale;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandHandlerInterface;
use Zentlix\UserBundle\Domain\Admin\Event\Setting\BeforeChangeLocale;
use Zentlix\UserBundle\Domain\Admin\Event\Setting\AfterChangeLocale;
use Zentlix\UserBundle\Domain\Admin\Service\AdminSettings;

class ChangeLocaleHandler implements CommandHandlerInterface
{
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;
    private AdminSettings $adminSettings;
    private SessionInterface $session;

    public function __construct(AdminSettings $adminSettings,
                                EntityManagerInterface $entityManager,
                                EventDispatcherInterface $eventDispatcher,
                                SessionInterface $session)
    {
        $this->adminSettings = $adminSettings;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->session = $session;
    }

    public function __invoke(ChangeLocaleCommand $command): void
    {
        $this->eventDispatcher->dispatch(new BeforeChangeLocale($command));

        $settings = $this->adminSettings->getSettings();
        $settings->setLocale($command->locale);

        $this->session->set('_locale', $command->locale->getCode());

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new AfterChangeLocale($settings->getLocale()));
    }
}