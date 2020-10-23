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
use Zentlix\MainBundle\UI\Http\Web\Controller\Admin\ResourceController;
use Zentlix\UserBundle\Application\Command\Group\CreateCommand;
use Zentlix\UserBundle\Application\Command\Group\UpdateCommand;
use Zentlix\UserBundle\Application\Command\Group\DeleteCommand;
use Zentlix\UserBundle\Application\Query\Group\DataTableQuery;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;
use Zentlix\UserBundle\UI\Http\Web\DataTable\Group\Table;
use Zentlix\UserBundle\UI\Http\Web\Form\Group\CreateForm;
use Zentlix\UserBundle\UI\Http\Web\Form\Group\UpdateForm;

class GroupController extends ResourceController
{
    public static $createSuccessMessage = 'zentlix_user.group.create.success';
    public static $updateSuccessMessage = 'zentlix_user.group.update.success';
    public static $deleteSuccessMessage = 'zentlix_user.group.delete.success';
    public static $redirectAfterAction  = 'admin.group.list';

    public function index(): Response
    {
        return $this->listResource(new DataTableQuery(Table::class),'@UserBundle/admin/groups/groups.html.twig');
    }

    public function create(): Response
    {
        return $this->createResource(new CreateCommand(), CreateForm::class, '@UserBundle/admin/groups/create.html.twig');
    }

    public function update(UserGroup $group): Response
    {
        return $this->updateResource(
            new UpdateCommand($group), UpdateForm::class, '@UserBundle/admin/groups/update.html.twig', ['group' => $group]
        );
    }

    public function delete(UserGroup $group): Response
    {
        return $this->deleteResource(new DeleteCommand($group));
    }
}