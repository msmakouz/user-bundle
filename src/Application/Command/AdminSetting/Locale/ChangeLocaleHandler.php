<?php

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\AdminSetting\Locale;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandHandlerInterface;
use Zentlix\UserBundle\Domain\Admin\Event\Setting\BeforeChangeLocale;
use Zentlix\UserBundle\Domain\Admin\Event\Setting\AfterChangeLocale;
use Zentlix\UserBundle\Domain\Admin\Service\AdminSettings;

class ChangeLocaleHandler implements CommandHandlerInterface
{
    public function __construct(
        private AdminSettings $adminSettings,
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher,
        private RequestStack $requestStack
    ) {
    }

    public function __invoke(ChangeLocaleCommand $command): void
    {
        $this->eventDispatcher->dispatch(new BeforeChangeLocale($command));

        $settings = $this->adminSettings->getSettings();
        $settings->setLocale($command->locale);

        $this->requestStack->getSession()->set('_locale', $command->locale->getCode());

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new AfterChangeLocale($settings->getLocale()));
    }
}
