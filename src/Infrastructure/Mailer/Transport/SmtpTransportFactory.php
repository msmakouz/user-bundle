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
use Zentlix\MainBundle\Domain\Attribute\Service\Attributes;
use Zentlix\MainBundle\Domain\Site\Service\Sites;

final class SmtpTransportFactory extends AbstractTransportFactory
{
    private ?string $host = null;
    private int $port = 0;
    private ?string $user = null;
    private ?string $password = null;

    public function __construct(Sites $sites,
                                Attributes $attributes,
                                EventDispatcherInterface $dispatcher = null,
                                HttpClientInterface $client = null,
                                LoggerInterface $logger = null)
    {
        if($sites->hasCurrentSite()) {
            $siteId = $sites->getCurrentSiteId();

            $this->host = $attributes->getAttributeValue('zentlix-user-smtp-host', $siteId, '127.0.0.1');
            $this->port = (int) $attributes->getAttributeValue('zentlix-user-smtp-port', $siteId, 0);
            $this->user = $attributes->getAttributeValue('zentlix-user-smtp-user', $siteId);;
            $this->password = $attributes->getAttributeValue('zentlix-user-smtp-password', $siteId);;
        }

        parent::__construct($dispatcher, $client, $logger);
    }

    public function create(Dsn $dsn): TransportInterface
    {
        $tls = 'zentlix+smtps' === $dsn->getScheme() ? true : null;

        $transport = new EsmtpTransport($this->host, $this->port, $tls, $this->dispatcher, $this->logger);

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