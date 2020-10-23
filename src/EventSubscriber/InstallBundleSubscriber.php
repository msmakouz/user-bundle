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
use Symfony\Contracts\Translation\TranslatorInterface;
use Zentlix\MainBundle\Domain\Bundle\Entity\Bundle;
use Zentlix\MainBundle\Domain\Bundle\Event\AfterInstall;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandBus;
use Zentlix\UserBundle\Application\Command\Group\CreateCommand;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;
use Zentlix\UserBundle\UserBundle;

class InstallBundleSubscriber implements EventSubscriberInterface
{
    private TranslatorInterface $translator;
    private CommandBus $commandBus;

    public function __construct(TranslatorInterface $translator, CommandBus $commandBus)
    {
        $this->translator = $translator;
        $this->commandBus = $commandBus;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterInstall::class => 'onAfterInstall',
        ];
    }

    public function onAfterInstall(AfterInstall $afterInstall): void
    {
        /** @var Bundle $bundle */
        $bundle = $afterInstall->getBundle();

        if($bundle->getClass() === UserBundle::class) {
            $command = new CreateCommand();
            $command->title = $this->translator->trans('zentlix_user.administrators');
            $command->code = UserGroup::ADMIN_GROUP;
            $command->group_role = UserGroup::GROUP_ROLE_ADMIN;
            $command->sort = 1;

            $this->commandBus->handle($command);

            $command = new CreateCommand();
            $command->title = $this->translator->trans('zentlix_user.user.users');
            $command->code = UserGroup::USER_GROUP;
            $command->group_role = UserGroup::GROUP_ROLE_USER;
            $command->sort = 2;

            $this->commandBus->handle($command);
        }
    }
}
