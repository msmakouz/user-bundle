<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zentlix\MainBundle\ZentlixBundleInterface;
use Zentlix\MainBundle\ZentlixBundleTrait;
use Zentlix\UserBundle\Application;

class UserBundle extends Bundle implements ZentlixBundleInterface
{
    use ZentlixBundleTrait;

    public function getVersion(): string
    {
        return '1.0.0';
    }

    public function getTitle(): string
    {
        return 'zentlix_user.bundle_title';
    }

    public function getDeveloper(): array
    {
        return ['name' => 'Zentlix', 'url' => 'https://zentlix.io'];
    }

    public function getDescription(): string
    {
        return 'zentlix_user.bundle_description';
    }

    public function configureRights(): array
    {
        return [
            Application\Query\User\DataTableQuery::class   => 'zentlix_user.user.view',
            Application\Command\User\CreateCommand::class  => 'zentlix_user.user.create.process',
            Application\Command\User\UpdateCommand::class  => 'zentlix_user.user.update.process',
            Application\Command\User\DeleteCommand::class  => 'zentlix_user.user.delete.process',
            Application\Query\Group\DataTableQuery::class  => 'zentlix_user.group.view',
            Application\Command\Group\CreateCommand::class => 'zentlix_user.group.create.process',
            Application\Command\Group\UpdateCommand::class => 'zentlix_user.group.update.process',
            Application\Command\Group\DeleteCommand::class => 'zentlix_user.group.delete.process'
        ];
    }

    public function isSystem(): bool
    {
        return true;
    }
}