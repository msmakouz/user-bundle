<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Mailer\Provider;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Twig\Environment;
use Zentlix\MainBundle\Domain\Attribute\Service\Attributes;
use Zentlix\MainBundle\Domain\Site\Service\Sites;
use Zentlix\UserBundle\Domain\Mailer\Entity\Template;
use Zentlix\UserBundle\Domain\Mailer\Event\BeforeSend;
use Zentlix\UserBundle\Infrastructure\Mailer\Provider\ProviderInterface;
use function is_null;

class Email implements ProviderInterface
{
    private EventDispatcherInterface $eventDispatcher;
    private MailerInterface $mailer;
    private Sites $sites;
    private Environment $twig;
    private Attributes $attributes;
    private string $defaultLayout;

    public function __construct(EventDispatcherInterface $eventDispatcher,
                                MailerInterface $mailer,
                                Sites $sites,
                                Attributes $attributes,
                                Environment $twig,
                                string $defaultLayout)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->mailer = $mailer;
        $this->sites = $sites;
        $this->attributes = $attributes;
        $this->twig = $twig;
        $this->defaultLayout = $defaultLayout;
    }

    public function getCode(): string
    {
        return 'email';
    }

    public function getTitle(): string
    {
        return 'zentlix_user.mailer.email';
    }

    public function send(Template $template, string $defaultTo, array $data = []): void
    {
        $sender = $this->getSenderEmail();
        if(is_null($sender)) {
            return;
        }

        $recipients = array_map(fn(string $email)
            => str_ireplace('%default_to%', $defaultTo, $email), explode(',', trim($template->getRecipient()))
        );

        $this->eventDispatcher->dispatch(new BeforeSend($template, $data));

        $email = (new TemplatedEmail())
            ->from(new Address($this->getSenderEmail(), $this->getSenderName()))
            ->to(array_shift($recipients))
            ->subject($template->getTheme())
            ->htmlTemplate($this->getEmailLayout())
            ->context([
                'site' => $this->sites->getCurrentSite(),
                'body' => $this->twig->createTemplate($template->getBody())->render($data),
                'theme' => $template->getTheme()
            ]);

        if(is_array($recipients)) {
            $email->bcc(...$recipients);
        }

        $this->mailer->send($email);
    }

    private function getSenderEmail(): ?string
    {
        $email = $this->attributes->getAttributeValue('zentlix-user-email', $this->sites->getCurrentSiteId());

        return !is_null($email) ? $email->getValue() : null;
    }

    private function getSenderName(): string
    {
        return $this->sites->getCurrentSite()->getTitle();
    }

    private function getEmailLayout(): string
    {
        $template = $this->sites->getCurrentSite()->getTemplate();

        $layout = null;
        if($template->getConfigParam('user.email_layout')) {
            $layout = DIRECTORY_SEPARATOR . $template->getFolder() . DIRECTORY_SEPARATOR . $template->getConfigParam('user.email_layout');
        }

        return $layout ?? $this->defaultLayout;
    }
}