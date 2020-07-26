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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints;
use Zentlix\MainBundle\Application\Command\UpdateCommandInterface;
use Zentlix\MainBundle\Domain\Site\Entity\Site;
use Zentlix\UserBundle\Domain\Mailer\Entity\Template;

class UpdateCommand extends Command implements UpdateCommandInterface
{
    /** @Constraints\NotBlank() */
    public ?string $code;

    public function __construct(Template $template, Request $request = null)
    {
        $this->entity = $template;

        $this->title = isset($request) ? $request->request->get('title', $template->getTitle()) : $template->getTitle();
        $this->active = isset($request) ? $request->request->get('active', $template->isActive()) : $template->isActive();
        $this->event = isset($request) ? $request->request->get('event', $template->getEvent()->getId()) : $template->getEvent()->getId();
        $this->provider = isset($request) ? $request->request->get('provider', $template->getProvider()) : $template->getProvider();
        $this->theme = isset($request) ? $request->request->get('theme', $template->getTheme()) : $template->getTheme();
        $this->code = isset($request) ? $request->request->get('code', $template->getCode()) : $template->getCode();
        $this->body = isset($request) ? $request->request->get('body', $template->getBody()) : $template->getBody();
        $this->recipient = isset($request) ? $request->request->get('recipient', $template->getRecipient()) : $template->getRecipient();

        /** @var Site $site */
        foreach ($template->getSites()->getValues() as $site) {
            $this->sites[$site->getId()] = $site->getTitle();
        }
    }

    public function getEntity(): Template
    {
        return $this->entity;
    }
}