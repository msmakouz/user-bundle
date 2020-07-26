<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Mailer\Specification;

use Symfony\Contracts\Translation\TranslatorInterface;
use Zentlix\MainBundle\Domain\Shared\Specification\AbstractSpecification;
use Zentlix\UserBundle\Domain\Mailer\Service\Providers;

final class ExistProviderSpecification extends AbstractSpecification
{
    private Providers $providers;
    private TranslatorInterface $translator;

    public function __construct(Providers $providers, TranslatorInterface $translator)
    {
        $this->providers = $providers;
        $this->translator = $translator;
    }

    public function isExist(string $code): bool
    {
        return $this->isSatisfiedBy($code);
    }

    public function isSatisfiedBy($value): bool
    {
        $provider = $this->providers->findProvider($value);

        if(is_null($provider)) {
            throw new \DomainException(sprintf($this->translator->trans('zentlix_user.mailer.provider_not_found'), $value));
        }

        return true;
    }

    public function __invoke(string $code)
    {
        return $this->isExist($code);
    }
}