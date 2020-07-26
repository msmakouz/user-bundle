<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\UI\Http\Web\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Zentlix\MainBundle\UI\Http\Web\Controller\Admin\ResourceController;
use Zentlix\UserBundle\Application\Command\User\CreateCommand;
use Zentlix\UserBundle\Application\Command\User\UpdateCommand;
use Zentlix\UserBundle\Application\Command\User\DeleteCommand;
use Zentlix\UserBundle\Application\Query\User\DataTableQuery;
use Zentlix\UserBundle\Domain\User\Entity\User;
use Zentlix\UserBundle\UI\Http\Web\DataTable\User\Table;
use Zentlix\UserBundle\UI\Http\Web\Form\User\CreateForm;
use Zentlix\UserBundle\UI\Http\Web\Form\User\UpdateForm;

class UserController extends ResourceController
{
    public static $createSuccessMessage = 'zentlix_user.user.create.success';
    public static $updateSuccessMessage = 'zentlix_user.user.update.success';
    public static $deleteSuccessMessage = 'zentlix_user.user.delete.success';
    public static $redirectAfterAction  = 'admin.user.list';

    public function index(Request $request): Response
    {
        return $this->listResource(new DataTableQuery(Table::class), $request);
    }

    public function create(Request $request): Response
    {
        return $this->createResource(new CreateCommand(), CreateForm::class, $request);
    }

    public function update(User $user, Request $request): Response
    {
        return $this->updateResource(new UpdateCommand($user), UpdateForm::class, $request);
    }

    public function delete(User $user): Response
    {
        return $this->deleteResource(new DeleteCommand($user));
    }

    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('admin.resolver');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@UserBundle/admin/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
}