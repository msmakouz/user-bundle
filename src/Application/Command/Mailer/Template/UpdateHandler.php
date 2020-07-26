<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\Mailer\Template;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zentlix\MainBundle\Application\Command\CommandHandlerInterface;
use Zentlix\MainBundle\Domain\Site\Repository\SiteRepository;
use Zentlix\MainBundle\Domain\Site\Specification\ExistSiteSpecification;
use Zentlix\UserBundle\Domain\Mailer\Specification\ExistEventSpecification;
use Zentlix\UserBundle\Domain\Mailer\Specification\ExistProviderSpecification;
use Zentlix\UserBundle\Domain\Mailer\Specification\UniqueCodeSpecification;
use Zentlix\UserBundle\Domain\Mailer\Event\Template\AfterUpdate;
use Zentlix\UserBundle\Domain\Mailer\Event\Template\BeforeUpdate;
use Zentlix\UserBundle\Domain\Mailer\Repository\EventRepository;
use Zentlix\UserBundle\Domain\Mailer\Service\Providers;

class UpdateHandler implements CommandHandlerInterface
{
    private ExistEventSpecification $existEventSpecification;
    private ExistProviderSpecification $existProviderSpecification;
    private ExistSiteSpecification $existSiteSpecification;
    private UniqueCodeSpecification $uniqueCodeSpecification;
    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;
    private EventRepository $eventRepository;
    private SiteRepository $siteRepository;
    private Providers $providers;

    public function __construct(EntityManagerInterface $entityManager,
                                EventDispatcherInterface $eventDispatcher,
                                ExistEventSpecification $existEventSpecification,
                                ExistProviderSpecification $existProviderSpecification,
                                ExistSiteSpecification $existSiteSpecification,
                                UniqueCodeSpecification $uniqueCodeSpecification,
                                EventRepository $eventRepository,
                                SiteRepository $siteRepository,
                                Providers $providers)
    {
        $this->existEventSpecification = $existEventSpecification;
        $this->existProviderSpecification = $existProviderSpecification;
        $this->existSiteSpecification = $existSiteSpecification;
        $this->uniqueCodeSpecification = $uniqueCodeSpecification;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->eventRepository = $eventRepository;
        $this->siteRepository = $siteRepository;
        $this->providers = $providers;
    }

    public function __invoke(UpdateCommand $command): void
    {
        $template = $command->getEntity();

        $this->existEventSpecification->isExist($command->event);
        $this->existProviderSpecification->isExist($command->provider);
        if(!$template->isCodeEqual($command->code)) {
            $this->uniqueCodeSpecification->isUnique($command->code);
        }

        foreach ($command->sites as $siteId) {
            $this->existSiteSpecification->isExist($siteId);
        }

        $command->event = $this->eventRepository->get($command->event);
        $command->sites = $this->siteRepository->findBy(['id' => $command->sites]);
        $command->provider_title = $this->providers->getProvider($command->provider)->getTitle();

        $this->eventDispatcher->dispatch(new BeforeUpdate($command));

        $template->update($command);

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new AfterUpdate($template, $command));
    }
}