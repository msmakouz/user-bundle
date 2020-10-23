<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Infrastructure\Mailer\Transport;

use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mailer\Transport\AbstractTransportFactory;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Zentlix\MainBundle\Domain\Site\Service\Sites;
use Zentlix\UserBundle\Domain\User\Repository\SiteRepository;

final class SmtpTransportFactory extends AbstractTransportFactory
{
    private ?string $host = null;
    private int $port = 0;
    private ?string $user = null;
    private ?string $password = null;

    public function __construct(Sites $sites,
                                SiteRepository $siteRepository,
                                EventDispatcherInterface $dispatcher = null,
                                HttpClientInterface $client = null,
                                LoggerInterface $logger = null)
    {
        $site = $siteRepository->getOneBySiteId($sites->getCurrentSiteId());

        $this->host = $site->getSmtpHost();
        $this->port = $site->getSmtpPort();
        $this->user = $site->getSmtpUser();
        $this->password = $site->getSmtpPassword();

        parent::__construct($dispatcher, $client, $logger);
    }

    public function create(Dsn $dsn): TransportInterface
    {
        $tls = 'zentlix+smtps' === $dsn->getScheme() ? true : null;
        $host = $this->host ?? '127.0.0.1';

        $transport = new EsmtpTransport($host, $this->port, $tls, $this->dispatcher, $this->logger);

        if ($this->user) {
            $transport->setUsername($this->user);
        }

        if ($this->password) {
            $transport->setPassword($this->password);
        }

        return $transport;
    }

    public function getSupportedSchemes(): array
    {
        return ['zentlix+smtp', 'zentlix+smtps'];
    }
}