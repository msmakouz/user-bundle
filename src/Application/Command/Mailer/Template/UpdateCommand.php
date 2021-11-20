<?php

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\Mailer\Template;

use Symfony\Component\Validator\Constraints;
use Zentlix\MainBundle\Domain\Site\Entity\Site;
use Zentlix\MainBundle\Infrastructure\Share\Bus\UpdateCommandInterface;
use Zentlix\UserBundle\Domain\Mailer\Entity\Template;

class UpdateCommand extends Command implements UpdateCommandInterface
{
    /** @Constraints\NotBlank() */
    public ?string $code;

    public function __construct(Template $template)
    {
        $this->entity = $template;

        $this->title     = $template->getTitle();
        $this->active    = $template->isActive();
        $this->event     = $template->getEvent();
        $this->provider  = $template->getProvider();
        $this->theme     = $template->getTheme();
        $this->code      = $template->getCode();
        $this->body      = $template->getBody();
        $this->recipient = $template->getRecipient();

        /** @var Site $site */
        foreach ($template->getSites()->getValues() as $site) {
            $this->sites[$site->getId()->toRfc4122()] = $site->getTitle();
        }
        $this->sites = array_flip($this->sites);
    }
}
