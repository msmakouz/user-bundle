<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\UI\Http\Web\DataTable\Mailer;

use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Zentlix\MainBundle\Domain\DataTable\Column\TextColumn;
use Zentlix\MainBundle\Infrastructure\Share\DataTable\AbstractDataTableType;
use Zentlix\UserBundle\Domain\Mailer\Event\Template\Table as TableEvent;
use Zentlix\UserBundle\Domain\Mailer\Entity\Template;

class Table extends AbstractDataTableType
{
    public function configure(DataTable $dataTable, array $options)
    {
        $dataTable->setName($this->router->generate('admin.mailer.list'));
        $dataTable->setTitle('zentlix_user.mailer.templates');
        $dataTable->setCreateBtnLabel('zentlix_user.mailer.create.action');

        $dataTable
            ->add('id', TextColumn::class, ['label' => 'zentlix_main.id', 'visible' => true])
            ->add('title', TextColumn::class,
                [
                    'render'  => fn($value, Template $context) => sprintf('<a href="%s">%s</a>',
                        $this->router->generate('admin.mailer.update', ['id' => $context->getId()]), $value),
                    'visible' => true,
                    'label'   => 'zentlix_main.title'
                ])
            ->add('provider_title', TextColumn::class, ['label' => 'main.type', 'visible' => true, 'translate' => true])
            ->add('event', TextColumn::class,
                [
                    'data'      => fn(Template $template) => $template->getEvent()->getTitle(),
                    'label'     => 'main.event',
                    'visible'   => true,
                    'translate' => true
                ])
            ->add('theme', TextColumn::class, ['label' => 'main.theme', 'visible' => false])
            ->add('code', TextColumn::class, ['label' => 'zentlix_main.symbol_code', 'visible' => false])
            ->add('recipient', TextColumn::class, ['label' => 'zentlix_user.mailer.recipient', 'visible' => false])
            ->createAdapter(ORMAdapter::class, ['entity' => Template::class]);

        $this->eventDispatcher->dispatch(new TableEvent($dataTable));
    }
}