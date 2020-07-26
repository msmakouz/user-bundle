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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Zentlix\MainBundle\Domain\Bundle\Repository\BundleRepository;
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

    public function index(Request $request): Response
    {
        return $this->listResource(new DataTableQuery(Table::class), $request);
    }

    public function create(Request $request): Response
    {
        return $this->createResource(new CreateCommand(), CreateForm::class, $request);
    }

    public function update(UserGroup $group, Request $request, BundleRepository $bundleRepository, ContainerInterface $container): Response
    {
        return $this->updateResource(new UpdateCommand($group, $bundleRepository, $container), UpdateForm::class, $request);
    }

    public function delete(UserGroup $group): Response
    {
        return $this->deleteResource(new DeleteCommand($group));
    }
}