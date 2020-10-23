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

use Zentlix\UserBundle\Infrastructure\Mailer\Provider\ProviderInterface;

class Providers
{
    private array $providers = [];

    public function __construct(iterable $providers)
    {
        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }

    public function addProvider(ProviderInterface $provider)
    {
        if(isset($this->providers[$provider->getCode()])) {
            throw new \DomainException(sprintf('Provider with code %s already exist.', $provider->getCode()));
        }

        $this->providers[$provider->getCode()] = $provider;
    }

    public function find(string $code): ?ProviderInterface
    {
        if(isset($this->providers[$code])) {
            return $this->providers[$code];
        }

        return null;
    }

    public function get(string $code): ProviderInterface
    {
        if(isset($this->providers[$code]) === false) {
            throw new \DomainException(sprintf('Provider with code %s not found.', $code));
        }

        return $this->providers[$code];
    }

    public function assoc(): array
    {
        $providers = [];
        foreach ($this->providers as $provider) {
            $providers[$provider->getTitle()] = $provider->getCode();
        }

        return $providers;
    }
}