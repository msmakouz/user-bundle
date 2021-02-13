<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\UI\Http\Web\DataTable\User;

use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TwigColumn;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\TextColumn;
use Zentlix\MainBundle\Infrastructure\DataTable\AbstractDataTableType;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;
use Zentlix\UserBundle\Domain\User\Entity\User;
use Zentlix\UserBundle\Domain\User\Event\Table as TableEvent;

class Table extends AbstractDataTableType
{
    public function configure(DataTable $dataTable, array $options)
    {
        $dataTable->setName('users-datatable');

        $dataTable
            ->add('id', TextColumn::class, ['label' => 'zentlix_main.id', 'visible' => false])
            ->add('email', TwigColumn::class,
                [
                    'template' => '@UserBundle/admin/users/datatable/title.html.twig',
                    'visible'  => true,
                    'label'    => 'zentlix_user.email'
                ])
            ->add('last_name', TextColumn::class, ['label' => 'zentlix_user.last_name', 'visible' => true])
            ->add('first_name', TextColumn::class, ['label' => 'zentlix_user.first_name', 'visible' => true])
            ->add('middle_name', TextColumn::class, ['label' => 'zentlix_user.middle_name', 'visible' => false])
            ->add('phone', TextColumn::class, ['label' => 'zentlix_user.phone', 'visible' => true])
            ->add('groups', TextColumn::class, [
                'data'      => fn(User $user) => implode(', ', array_map(fn(UserGroup $group) => $group->getTitle(), $user->getGroups()->getValues())),
                'label'     => 'zentlix_user.group.groups',
                'visible'   => true,
                'orderable' => false
            ])
            ->add('zip', TextColumn::class, ['label' => 'zentlix_user.zip', 'visible' => false])
            ->add('country', TextColumn::class, ['label' => 'zentlix_user.country', 'visible' => false])
            ->add('city', TextColumn::class, ['label' => 'zentlix_user.city', 'visible' => false])
            ->add('street', TextColumn::class, ['label' => 'zentlix_user.street', 'visible' => false])
            ->add('house', TextColumn::class, ['label' => 'zentlix_user.house', 'visible' => false])
            ->add('flat', TextColumn::class, ['label' => 'zentlix_user.flat', 'visible' => false])
            ->add('email_confirmed', TextColumn::class,
                [
                    'label'   => 'zentlix_user.email_confirmed',
                    'visible' => false,
                    'data'    => fn(User $user) => $user->isEmailConfirmed() ? $this->translator->trans('zentlix_main.yes') : $this->translator->trans('zentlix_main.no')
                ])
            ->add('status', TextColumn::class,
                [
                    'label' => 'zentlix_user.status',
                    'visible' => true,
                    'data' => function(User $user) {
                        switch ($user->getStatus()) {
                            case User::STATUS_ACTIVE:
                                return $this->translator->trans('zentlix_user.active');
                            case User::STATUS_BLOCKED:
                                return $this->translator->trans('zentlix_user.blocked');
                            default:
                                return $this->translator->trans('zentlix_user.wait_activation');
                        }
                    }
                ])
            ->add('created_at', DateTimeColumn::class,
                [
                    'label'   => 'zentlix_user.register_at',
                    'format'  => 'd-m-Y H:i:s',
                    'visible' => false
                ])
            ->add('last_login', DateTimeColumn::class,
                [
                    'label'   => 'zentlix_user.last_login',
                    'visible' => false,
                    'format'  => 'd-m-Y H:i:s',
                ])
            ->add('updated_at', DateTimeColumn::class,
                [
                    'label'   => 'zentlix_user.updated_at',
                    'visible' => false,
                    'format'  => 'd-m-Y H:i:s',
                ])
            ->addOrderBy($dataTable->getColumnByName('id'), $dataTable::SORT_DESCENDING)
            ->createAdapter(ORMAdapter::class, ['entity' => User::class]);

        $this->eventDispatcher->dispatch(new TableEvent($dataTable));
    }
}