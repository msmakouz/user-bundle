<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\EventListener;

use Symfony\Contracts\Translation\TranslatorInterface;
use Zentlix\MainBundle\Application\Command\Attribute;
use Zentlix\MainBundle\Domain\Attribute\Type\NumberType;
use Zentlix\MainBundle\Domain\Attribute\Type\StringType;
use Zentlix\MainBundle\Domain\Bundle\Entity\Bundle;
use Zentlix\MainBundle\Domain\Bundle\Event\AfterInstall;
use Zentlix\MainBundle\Domain\Site\Entity\Site;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandBus;
use Zentlix\UserBundle\Application\Command\Group;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;
use Zentlix\UserBundle\UserBundle;

class BundleListener
{
    private TranslatorInterface $translator;
    private CommandBus $commandBus;

    public function __construct(TranslatorInterface $translator, CommandBus $commandBus)
    {
        $this->translator = $translator;
        $this->commandBus = $commandBus;
    }

    public function __invoke(AfterInstall $afterInstall): void
    {
        /** @var Bundle $bundle */
        $bundle = $afterInstall->getBundle();

        if($bundle->getClass() === UserBundle::class) {
            $this->createAttributes();
            $this->createUserGroups();
        }
    }

    private function createAttributes(): void
    {
        $command = new Attribute\CreateCommand(Site::getEntityCode());

        $command->bundle         = UserBundle::class;
        $command->editable       = false;
        $command->attribute_type = StringType::getCode();
        $command->title          = 'zentlix_user.email';
        $command->sort           = 1;
        $command->code           = 'zentlix-user-email';
        $command->config = [
            'required' => false,
            'type'     => 'email'
        ];

        $this->commandBus->handle($command);

        $command = new Attribute\CreateCommand(Site::getEntityCode());

        $command->bundle         = UserBundle::class;
        $command->editable       = false;
        $command->attribute_type = StringType::getCode();
        $command->title          = 'zentlix_user.smtp_host';
        $command->code           = 'zentlix-user-smtp-host';
        $command->sort           = 2;
        $command->config = [
            'required' => false,
            'type'     => 'text'
        ];

        $this->commandBus->handle($command);

        $command = new Attribute\CreateCommand(Site::getEntityCode());

        $command->bundle         = UserBundle::class;
        $command->editable       = false;
        $command->attribute_type = NumberType::getCode();
        $command->title          = 'zentlix_user.smtp_port';
        $command->code           = 'zentlix-user-smtp-port';
        $command->sort           = 3;
        $command->config = [
            'required' => false,
            'integer'  => true
        ];

        $this->commandBus->handle($command);

        $command = new Attribute\CreateCommand(Site::getEntityCode());

        $command->bundle         = UserBundle::class;
        $command->editable       = false;
        $command->attribute_type = StringType::getCode();
        $command->title          = 'zentlix_user.user.user';
        $command->code           = 'zentlix-user-smtp-user';
        $command->sort           = 4;
        $command->config = [
            'required' => false,
            'type'     => 'text'
        ];

        $this->commandBus->handle($command);

        $command = new Attribute\CreateCommand(Site::getEntityCode());

        $command->bundle         = UserBundle::class;
        $command->editable       = false;
        $command->attribute_type = StringType::getCode();
        $command->title          = 'zentlix_user.password';
        $command->code           = 'zentlix-user-smtp-password';
        $command->sort           = 5;
        $command->config = [
            'required' => false,
            'type'     => 'password'
        ];

        $this->commandBus->handle($command);
    }

    private function createUserGroups(): void
    {
        $command = new Group\CreateCommand();
        $command->title      = $this->translator->trans('zentlix_user.administrators');
        $command->code       = UserGroup::ADMIN_GROUP;
        $command->group_role = UserGroup::GROUP_ROLE_ADMIN;
        $command->sort       = 1;

        $this->commandBus->handle($command);

        $command = new Group\CreateCommand();
        $command->title      = $this->translator->trans('zentlix_user.user.users');
        $command->code       = UserGroup::USER_GROUP;
        $command->group_role = UserGroup::GROUP_ROLE_USER;
        $command->sort       = 2;

        $this->commandBus->handle($command);
    }
}
