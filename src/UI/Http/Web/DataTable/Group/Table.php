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
use Omines\DataTablesBundle\DataTable;
use Zentlix\MainBundle\Domain\DataTable\Column\TextColumn;
use Zentlix\MainBundle\Infrastructure\Share\DataTable\AbstractDataTableType;
use Zentlix\UserBundle\Domain\Group\Event\Table as TableEvent;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;

class Table extends AbstractDataTableType
{
    public function configure(DataTable $dataTable, array $options)
    {
        $dataTable->setName($this->router->generate('admin.group.list'));
        $dataTable->setTitle('zentlix_user.group.groups');
        $dataTable->setCreateBtnLabel('zentlix_user.group.create.action');

        $dataTable
            ->add('id', TextColumn::class, ['label' => 'zentlix_main.id', 'visible' => true])
            ->add('title', TextColumn::class,
                [
                    'render' => fn($value, UserGroup $context) =>
                        sprintf('<a href="%s">%s</a>', $this->router->generate('admin.group.update', ['id' => $context->getId()]), $value),
                    'visible' => true,
                    'label' => 'zentlix_main.title'
                ])
            ->add('group_role', TextColumn::class,
                [
                    'label' => 'zentlix_user.group_access',
                    'visible' => true,
                    'data' => function(UserGroup $user) {
                        switch ($user->getGroupRole()) {
                            case UserGroup::GROUP_ROLE_ADMIN:
                                return $this->translator->trans('zentlix_user.role_admin');
                                break;
                            default:
                                return $this->translator->trans('zentlix_user.role_user');
                        }
                    }
                ])
            ->add('sort', TextColumn::class, ['visible' => true, 'label' => 'zentlix_main.sort'])
            ->add('code', TextColumn::class, ['visible' => false, 'label' => 'zentlix_main.symbol_code'])
            ->addOrderBy($dataTable->getColumnByName('sort'))
            ->addOrderBy($dataTable->getColumnByName('id'), $dataTable::SORT_DESCENDING)
            ->createAdapter(ORMAdapter::class, ['entity' => UserGroup::class]);

        $this->eventDispatcher->dispatch(new TableEvent($dataTable));
    }
}