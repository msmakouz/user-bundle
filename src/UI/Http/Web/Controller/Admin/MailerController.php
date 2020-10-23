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
use Zentlix\UserBundle\Application\Command\Mailer\Template\CreateCommand;
use Zentlix\UserBundle\Application\Command\Mailer\Template\DeleteCommand;
use Zentlix\UserBundle\Application\Command\Mailer\Template\UpdateCommand;
use Zentlix\UserBundle\Application\Query\Mailer\Template\DataTableQuery;
use Zentlix\UserBundle\Domain\Mailer\Entity\Template;
use Zentlix\UserBundle\UI\Http\Web\DataTable\Mailer\Table;
use Zentlix\UserBundle\UI\Http\Web\Form\Mailer\CreateForm;
use Zentlix\UserBundle\UI\Http\Web\Form\Mailer\UpdateForm;

class MailerController extends ResourceController
{
    public static $createSuccessMessage = 'zentlix_user.mailer.create.success';
    public static $updateSuccessMessage = 'zentlix_user.mailer.update.success';
    public static $deleteSuccessMessage = 'zentlix_user.mailer.delete.success';
    public static $redirectAfterAction  = 'admin.mailer.list';

    public function index(): Response
    {
        return $this->listResource(new DataTableQuery(Table::class),'@UserBundle/admin/mailer/templates.html.twig');
    }

    public function create(): Response
    {
        return $this->createResource(new CreateCommand(), CreateForm::class, '@UserBundle/admin/mailer/create.html.twig');
    }

    public function update(Template $template): Response
    {
        return $this->updateResource(
            new UpdateCommand($template), UpdateForm::class, '@UserBundle/admin/mailer/update.html.twig', ['template' => $template]
        );
    }

    public function delete(Template $template): Response
    {
        return $this->deleteResource(new DeleteCommand($template));
    }
}