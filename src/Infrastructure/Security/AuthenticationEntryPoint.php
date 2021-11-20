<?php

declare(strict_types=1);

namespace Zentlix\UserBundle\Infrastructure\Security;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private ContainerInterface $container
    ) {
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        if(strpos($this->urlGenerator->getContext()->getPathInfo(), $this->container->getParameter('admin_path'))) {
            return new RedirectResponse($this->urlGenerator->generate('admin.login'));
        }

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }
}
