<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\UI\Http\Web\DataTable\Group;

use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTable;
use Zentlix\MainBundle\Infrastructure\Share\DataTable\AbstractDataTableType;
use Zentlix\UserBundle\Domain\Group\Event\Table as TableEvent;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;

class Table extends AbstractDataTableType
{
    public function configure(DataTable $dataTable, array $options)
    {
        $dataTable->setName('groups-datatable');

        $dataTable
            ->add('id', TextColumn::class, ['label' => 'zentlix_main.id', 'visible' => true])
            ->add('title', TwigColumn::class,
                [
                    'template' => '@UserBundle/admin/groups/datatable/title.html.twig',
                    'visible'  => true,
                    'label'    => 'zentlix_main.title'
                ])
            ->add('group_role', TextColumn::class,
                [
                    'label'   => 'zentlix_user.group_access',
                    'visible' => true,
                    'data'    => fn(UserGroup $user) => $user->getGroupRole() === UserGroup::GROUP_ROLE_ADMIN ?
                        $this->translator->trans('zentlix_user.role_admin') : $this->translator->trans('zentlix_user.role_user')
                ])
            ->add('sort', TextColumn::class, ['visible' => true, 'label' => 'zentlix_main.sort'])
            ->add('code', TextColumn::class, ['visible' => false, 'label' => 'zentlix_main.symbol_code'])
            ->addOrderBy($dataTable->getColumnByName('sort'))
            ->addOrderBy($dataTable->getColumnByName('id'), $dataTable::SORT_DESCENDING)
            ->createAdapter(ORMAdapter::class, ['entity' => UserGroup::class]);

        $this->eventDispatcher->dispatch(new TableEvent($dataTable));
    }
}