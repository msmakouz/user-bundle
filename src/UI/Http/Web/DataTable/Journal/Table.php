<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\UI\Http\Web\DataTable\Journal;

use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Zentlix\MainBundle\Infrastructure\Share\DataTable\AbstractDataTableType;
use Zentlix\UserBundle\Domain\Journal\Event\Table as TableEvent;
use Zentlix\UserBundle\Domain\Journal\Entity\Journal;

class Table extends AbstractDataTableType
{
    public function configure(DataTable $dataTable, array $options)
    {
        $dataTable->setName('journal-datatable');

        $dataTable
            ->add('email', TextColumn::class, [
                'visible' => true,
                'label'   => 'zentlix_user.email',
                'data'    => fn(Journal $journal) => $journal->getEmail()->getValue()
            ])
            ->add('ip', TextColumn::class, ['visible' => true, 'label' => 'zentlix_user.ip_address'])
            ->add('date', DateTimeColumn::class,
                [
                    'label'      => 'zentlix_user.authorization.date',
                    'visible'    => true,
                    'format'     => 'd-m-Y H:i:s',
                    'searchable' => false
                ])
            ->add('success', TextColumn::class, [
                'visible'   => true,
                'label'     => 'zentlix_main.result',
                'data'      => fn(Journal $journal) => $journal->isSuccess() ?
                    $this->translator->trans('zentlix_main.success') : $this->translator->trans('zentlix_main.error'),
            ])
            ->add('reason', TextColumn::class, [
                'visible'   => true,
                'label'     => 'zentlix_user.authorization.reason_error',
                'data'      => fn(Journal $journal) => $this->translator->trans($journal->getReason())
            ])
            ->addOrderBy($dataTable->getColumnByName('date'), $dataTable::SORT_DESCENDING)
            ->createAdapter(ORMAdapter::class, ['entity' => Journal::class]);

        $this->eventDispatcher->dispatch(new TableEvent($dataTable));
    }
}