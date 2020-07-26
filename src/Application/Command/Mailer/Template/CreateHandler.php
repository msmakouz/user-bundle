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
use Zentlix\UserBundle\Domain\Mailer\Event\Template\AfterCreate;
use Zentlix\UserBundle\Domain\Mailer\Event\Template\BeforeCreate;
use Zentlix\UserBundle\Domain\Mailer\Entity\Template;
use Zentlix\UserBundle\Domain\Mailer\Repository\EventRepository;
use Zentlix\UserBundle\Domain\Mailer\Specification\ExistEventSpecification;
use Zentlix\UserBundle\Domain\Mailer\Specification\ExistProviderSpecification;
use Zentlix\UserBundle\Domain\Mailer\Specification\UniqueCodeSpecification;
use Zentlix\UserBundle\Domain\Mailer\Service\Providers;

class CreateHandler implements CommandHandlerInterface
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

    public function __invoke(CreateCommand $command): void
    {
        $this->existEventSpecification->isExist($command->event);
        $this->existProviderSpecification->isExist($command->provider);
        foreach ($command->sites as $siteId) {
            $this->existSiteSpecification->isExist($siteId);
        }
        if($command->code) {
            $this->uniqueCodeSpecification->isUnique($command->code);
        }

        $command->event = $this->eventRepository->get($command->event);
        $command->sites = $this->siteRepository->findBy(['id' => $command->sites]);
        $command->provider_title = $this->providers->getProvider($command->provider)->getTitle();

        $this->eventDispatcher->dispatch(new BeforeCreate($command));

        $template = new Template($command);

        $this->entityManager->persist($template);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new AfterCreate($template, $command));
    }
}