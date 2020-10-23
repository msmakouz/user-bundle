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

use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\DataTable;
use Zentlix\MainBundle\Infrastructure\Share\DataTable\AbstractDataTableType;
use Zentlix\UserBundle\Domain\Mailer\Event\Template\Table as TableEvent;
use Zentlix\UserBundle\Domain\Mailer\Entity\Template;

class Table extends AbstractDataTableType
{
    public function configure(DataTable $dataTable, array $options)
    {
        $dataTable->setName('mailer-datatable');
        $events = $this->container->get('zentlix_user.mailer_events');
        $providers = $this->container->get('zentlix_user.mailer_providers');

        $dataTable
            ->add('id', TextColumn::class, ['label' => 'zentlix_main.id', 'visible' => true])
            ->add('title', TwigColumn::class,
                [
                    'template' => '@UserBundle/admin/mailer/datatable/title.html.twig',
                    'visible'  => true,
                    'label'    => 'zentlix_main.title'
                ])
            ->add('provider', TextColumn::class, [
                'label'     => 'zentlix_user.mailer.provider',
                'visible'   => true,
                'render'    => fn($value) => $this->translator->trans($providers->get($value)->getTitle())
            ])
            ->add('event', TextColumn::class,
                [
                    'data'      => fn(Template $template) => $template->getEvent(),
                    'label'     => 'zentlix_user.mailer.event',
                    'visible'   => true,
                    'render'    => fn($value) => $this->translator->trans($events->get($value)->getTitle())
                ])
            ->add('theme', TextColumn::class, ['label' => 'main.theme', 'visible' => false])
            ->add('code', TextColumn::class, ['label' => 'zentlix_main.symbol_code', 'visible' => false])
            ->add('recipient', TextColumn::class, ['label' => 'zentlix_user.mailer.recipient', 'visible' => false])
            ->createAdapter(ORMAdapter::class, ['entity' => Template::class]);

        $this->eventDispatcher->dispatch(new TableEvent($dataTable));
    }
}