<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Mailer\Service;

use Zentlix\MainBundle\Domain\Site\Service\Sites;
use Zentlix\UserBundle\Domain\Mailer\Repository\EventRepository;
use Zentlix\UserBundle\Domain\Mailer\Repository\TemplateRepository;
use Zentlix\UserBundle\Infrastructure\Share\Mailer\Service\MailerInterface;

class Mailer implements MailerInterface
{
    private Providers $providers;
    private TemplateRepository $templateRepository;
    private EventRepository $eventRepository;
    private Sites $sites;

    public function __construct(Providers $providers,
                                TemplateRepository $templateRepository,
                                EventRepository $eventRepository,
                                Sites $sites)
    {
        $this->providers = $providers;
        $this->templateRepository = $templateRepository;
        $this->eventRepository = $eventRepository;
        $this->sites = $sites;
    }

    public function send(string $event, string $defaultTo, array $data = []): void
    {
        $eventId = $this->eventRepository->getOneByCode($event)->getId();

        $templates = $this->templateRepository->findActiveByEventSiteId($eventId, $this->sites->getCurrentSiteId());

        foreach ($templates as $template) {
            $provider = $this->providers->getProvider($template->getProvider());
            $provider->send($template, $defaultTo, $data);
        }
    }
}