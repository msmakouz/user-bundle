<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\UI\Http\Web\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Zentlix\UserBundle\Application\Command\User\CreateCommand;
use Zentlix\UserBundle\Domain\User\Service\Authenticator;
use Zentlix\UserBundle\UI\Http\Web\Form\User\RegistrationForm;
use Zentlix\MainBundle\UI\Http\Web\Controller\AbstractController;

class UserController extends AbstractController
{
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render($this->template->getConfigParam('user.login', $this->container->getParameter('zentlix_user.login_template')), [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    public function profile(): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') === false) {
            return $this->redirectToRoute('index');
        }

        return $this->render($this->template->getConfigParam('user.profile', $this->container->getParameter('zentlix_user.profile_template')), [
            'user' => $this->getUser()
        ]);
    }

    public function register(Request $request, GuardAuthenticatorHandler $guardHandler, Authenticator $authenticator): Response
    {
        $command = new CreateCommand();
        $form = $this->createForm(RegistrationForm::class, $command);

        try {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->exec($command);

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $command->user,
                    $request,
                    $authenticator,
                    'user_secured_area'
                );
            }
        } catch (\Exception $e) {
            return $this->render($this->template->getConfigParam('user.registration', $this->container->getParameter('zentlix_user.register_template')), [
                'form' => $form->createView(),
                'error' => $e->getMessage()
            ]);
        }

        return $this->render($this->template->getConfigParam('user.registration', $this->container->getParameter('zentlix_user.register_template')), [
            'form' => $form->createView()
        ]);
    }

    public function logout(): Response {}
}