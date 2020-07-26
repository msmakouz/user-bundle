<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\UI\Http\Web\DashboardWidget;

use Zentlix\MainBundle\Domain\Dashboard\Widgets\Card\AbstractProgressbarWidget;
use Zentlix\UserBundle\Domain\User\Repository\UserRepository;

class UsersCount extends AbstractProgressbarWidget
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getTitle(): string
    {
        return 'zentlix_user.widgets.count_users';
    }

    public function getText(): string
    {
        return 'zentlix_user.widgets.users';
    }

    public function getHelpText(): string
    {
        return 'zentlix_user.widgets.users_help';
    }

    public function getProgressbarPercent(): float
    {
        return 100;
    }

    public function getValue(): int
    {
        return $this->userRepository->count([]);
    }

    public function getBackgroundGradient(): string
    {
        return self::BACKGROUND_LIGHT_BlUE_GRADIENT;
    }

    public function getProgressbarBackgroundGradient(): string
    {
        return self::PROGRESSBAR_BACKGROUND_COLOR_WHITE;
    }

    public function getColor(): string
    {
        return '#fff';
    }

    public function getHelpTextColor(): string
    {
        return 'rgba(255,255,255,.6)';
    }
}