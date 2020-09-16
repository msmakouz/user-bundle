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
use Zentlix\UserBundle\Domain\Mailer\Entity\Event;

class UserBundle extends Bundle implements ZentlixBundleInterface
{
    use ZentlixBundleTrait;

    public function getVersion(): string
    {
        return '0.3.4';
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

    public function installMailerEvents(): array
    {
        $register = [
            'user.email'       => 'zentlix_user.user.email',
            'user.first_name'  => 'zentlix_user.first_name',
            'user.last_name'   => 'zentlix_user.last_name',
            'user.middle_name' => 'zentlix_user.middle_name',
            'user.phone'       => 'zentlix_user.phone_number',
            'user.zip'         => 'zentlix_user.zip',
            'user.country'     => 'zentlix_user.country',
            'user.city'        => 'zentlix_user.city',
            'user.street'      => 'zentlix_user.street',
            'user.house'       => 'zentlix_user.house',
            'user.flat'        => 'zentlix_user.flat'
        ];

        return [
            new Event('zentlix_user.register_user', $register, 'user-registration'),
            //new Event('zentlix_user.reset_password', ['link' => 'zentlix_user.reset_password_link'], 'reset-password')
        ];
    }

    public function isSystem(): bool
    {
        return true;
    }
}