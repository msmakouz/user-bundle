<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zentlix\MainBundle\Domain\AdminSidebar\Event\BeforeBuild;
use Zentlix\MainBundle\Domain\AdminSidebar\Event\AfterBuild;
use Zentlix\MainBundle\Domain\AdminSidebar\Service\MenuItemInterface;

class SidebarSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeBuild::class => 'onBeforeBuild',
            AfterBuild::class => 'onAfterBuild'
        ];
    }

    public function onBeforeBuild(BeforeBuild $beforeBuild): void
    {
        $sidebar = $beforeBuild->getSidebar();

        $users = $sidebar->addMenuItem('zentlix_user.user.users')
            ->url('/users/')
            ->icon(MenuItemInterface::ICON_PEOPLE)
            ->sort(140);

        $users
            ->addChildren('zentlix_user.user.users')
            ->generateUrl('admin.user.list')
            ->sort(100);

        $users
            ->addChildren('zentlix_user.group.groups')
            ->generateUrl('admin.group.list')
            ->sort(110);
    }

    public function onAfterBuild(AfterBuild $afterBuild)
    {
        $sidebar = $afterBuild->getSidebar();

        $settings = $sidebar->getMenuItem('zentlix_main.settings');

        $settings
            ->addChildren('zentlix_user.mailer.templates')
            ->generateUrl('admin.mailer.list')
            ->sort(100);
    }
}