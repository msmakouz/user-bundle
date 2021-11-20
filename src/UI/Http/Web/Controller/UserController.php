<?php

declare(strict_types=1);

namespace Zentlix\UserBundle\UI\Http\Web\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Zentlix\MainBundle\Domain\Site\Service\Sites;
use Zentlix\MainBundle\Infrastructure\Share\Bus;
use Zentlix\MainBundle\UI\Http\Web\Controller\AbstractSiteController;
use Zentlix\UserBundle\Application\Command\User\CreateCommand;
use Zentlix\UserBundle\UI\Http\Web\Form\User\RegistrationForm;

class UserController extends AbstractSiteController
{
    public function __construct(
        Sites $sites,
        HttpFoundation\RequestStack
        $requestStack,
        SerializerInterface
        $serializer,
        AuthorizationCheckerInterface
        $authorizationChecker,
        FormFactoryInterface $formFactory,
        ParameterBag $parameterBag,
        RouterInterface $router,
        TranslatorInterface $translator,
        Environment $twig,
        TokenStorageInterface $tokenStorage,
        Bus\CommandBus $commandBus,
        Bus\QueryBus $queryBus,
        private UserAuthenticatorInterface $userAuthenticator,
        private FormLoginAuthenticator $authenticator
    )
    {
        parent::__construct(
            $sites,
            $requestStack,
            $serializer,
            $authorizationChecker,
            $formFactory,
            $parameterBag,
            $router,
            $translator,
            $twig,
            $tokenStorage,
            $commandBus,
            $queryBus
        );
    }

    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render($this->template->getConfigParam('user.login', $this->parameterBag->get('zentlix_user.login_template')), [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    public function profile(): Response
    {
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') === false) {
            return $this->redirectToRoute('index');
        }

        return $this->render($this->template->getConfigParam('user.profile', $this->parameterBag->get('zentlix_user.profile_template')), [
            'user' => $this->getUser()
        ]);
    }

    public function register(Request $request): Response
    {
        $command = new CreateCommand();
        $form = $this->formFactory->create(RegistrationForm::class, $command);

        try {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->exec($command);

                return $this->userAuthenticator->authenticateUser(
                    $command->user,
                    $this->authenticator,
                    $this->requestStack->getCurrentRequest()
                );
            }
        } catch (\Exception $e) {
            return $this->render($this->template->getConfigParam('user.registration', $this->parameterBag->get('zentlix_user.register_template')), [
                'form' => $form->createView(),
                'error' => $e->getMessage()
            ]);
        }

        return $this->render($this->template->getConfigParam('user.registration', $this->parameterBag->get('zentlix_user.register_template')), [
            'form' => $form->createView()
        ]);
    }
}
