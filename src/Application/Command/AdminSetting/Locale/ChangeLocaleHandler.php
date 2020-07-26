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
use Zentlix\MainBundle\Application\Command\CommandHandlerInterface;
use Zentlix\MainBundle\Domain\Locale\Specification\ExistLocaleSpecification;
use Zentlix\UserBundle\Domain\Admin\Service\AdminSettings;
use Zentlix\MainBundle\Domain\Locale\Repository\LocaleRepository;
use Zentlix\UserBundle\Domain\Admin\Event\Setting\BeforeChangeLocale;
use Zentlix\UserBundle\Domain\Admin\Event\Setting\AfterChangeLocale;

class ChangeLocaleHandler implements CommandHandlerInterface
{
    private ExistLocaleSpecification $existLocaleSpecification;
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;
    private AdminSettings $adminSettings;
    private LocaleRepository $localeRepository;
    private SessionInterface $session;

    public function __construct(ExistLocaleSpecification $existLocaleSpecification,
                                AdminSettings $adminSettings,
                                LocaleRepository $localeRepository,
                                EntityManagerInterface $entityManager,
                                EventDispatcherInterface $eventDispatcher,
                                SessionInterface $session)
    {
        $this->existLocaleSpecification = $existLocaleSpecification;
        $this->adminSettings = $adminSettings;
        $this->localeRepository = $localeRepository;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->session = $session;
    }

    public function __invoke(ChangeLocaleCommand $command): void
    {
        $this->existLocaleSpecification->isExist($command->locale_id);

        $this->eventDispatcher->dispatch(new BeforeChangeLocale($command));

        $locale = $this->localeRepository->get($command->locale_id);
        $settings = $this->adminSettings->getSettings();
        $settings->setLocale($locale);

        $this->session->set('_locale', $locale->getCode());

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new AfterChangeLocale($settings->getLocale()));
    }
}