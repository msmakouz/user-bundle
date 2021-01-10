<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\User\Service;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Zentlix\UserBundle\Domain\Journal\Entity\Journal;
use Zentlix\UserBundle\Domain\User\Event\BeforeLogin;
use Zentlix\UserBundle\Domain\User\Event\AfterLogin;
use Zentlix\UserBundle\Domain\User\Entity\User;
use Zentlix\UserBundle\Domain\User\ValueObject\Email;

class Authenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private EntityManagerInterface $entityManager;
    private UrlGeneratorInterface $urlGenerator;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private UserPasswordEncoderInterface $passwordEncoder;
    private ContainerInterface $container;
    private TranslatorInterface $translator;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EntityManagerInterface $entityManager,
                                UrlGeneratorInterface $urlGenerator,
                                CsrfTokenManagerInterface $csrfTokenManager,
                                UserPasswordEncoderInterface $passwordEncoder,
                                ContainerInterface $container,
                                TranslatorInterface $translator,
                                EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->container = $container;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function supports(Request $request)
    {
        return ('app_login' === $request->attributes->get('_route') || 'admin.login' === $request->attributes->get('_route'))
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $beforeUserLogin = new BeforeLogin(new Email($request->request->get('email')), $request->request->get('password'));
        $this->eventDispatcher->dispatch($beforeUserLogin);

        $credentials = [
            'email'      => $beforeUserLogin->getEmail()->getValue(),
            'password'   => $beforeUserLogin->getPassword(),
            'csrf_token' => $request->request->get('_csrf_token'),
            'ip'         => $request->getClientIp()
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

        if (!$user) {
            $this->addJournal($credentials['email'], $credentials['ip'], false, 'zentlix_user.validation.user_not_exist');
            throw new CustomUserMessageAuthenticationException($this->translator->trans('zentlix_user.validation.user_not_exist'));
        }

        if($user->isWait()) {
            $this->addJournal($credentials['email'], $credentials['ip'], false, 'zentlix_user.user_wait');
            throw new CustomUserMessageAuthenticationException($this->translator->trans('zentlix_user.user_wait'));
        }

        if($user->isBlocked()) {
            $this->addJournal($credentials['email'], $credentials['ip'], false, 'zentlix_user.user_blocked');
            throw new CustomUserMessageAuthenticationException($this->translator->trans('zentlix_user.user_blocked'));
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if($this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            $this->addJournal($credentials['email'], $credentials['ip']);
            $this->eventDispatcher->dispatch(new AfterLogin($user));

            return true;
        }

        $this->addJournal($credentials['email'], $credentials['ip'], false, 'zentlix_user.authorization.bad_password');

        return false;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        if(strpos($this->urlGenerator->getContext()->getPathInfo(), $this->container->getParameter('admin_path'))) {
            return new RedirectResponse($this->urlGenerator->generate('admin.login'));
        }

        return new RedirectResponse($this->urlGenerator->generate('app_login'));
    }

    protected function getLoginUrl()
    {
        if(strpos($this->urlGenerator->getContext()->getPathInfo(), $this->container->getParameter('admin_path'))) {
            return $this->urlGenerator->generate('admin.login');
        }

        return $this->urlGenerator->generate('app_login');
    }

    private function addJournal(string $email, string $ip, bool $success = true, string $reason = null): void
    {
        $this->entityManager->persist(new Journal($email, $ip, $success, $reason));
        $this->entityManager->flush();
    }
}