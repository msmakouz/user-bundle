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
use Zentlix\MainBundle\Domain\Route\Entity\Route;
use Zentlix\MainBundle\ZentlixBundleTrait;
use Zentlix\UserBundle\Application;
use Zentlix\UserBundle\Domain\Mailer\Entity\Event;
use Zentlix\UserBundle\UI\Http\Web\Controller;

class UserBundle extends Bundle
{
    use ZentlixBundleTrait;

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

    public function installFrontendRoutes(): array
    {
        return [
            new Route('profile', Controller\UserController::class, 'profile', 'zentlix_user.profile', 'user.profile'),
            new Route('register', Controller\UserController::class, 'register', 'zentlix_user.register', 'user.register'),
            new Route('login', Controller\UserController::class, 'login', 'zentlix_user.login', 'user.login'),
        ];
    }

    public function installMailEvents(): array
    {
        return [
            new Event('user.register_user', ['user.email' => 'zentlix_user.user.email'], 'user-registration'),
            new Event('user.reset_password', ['link' => 'zentlix_user.reset_password_link'], 'reset-password')
        ];
    }
}