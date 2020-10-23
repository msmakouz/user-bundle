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
use Zentlix\UserBundle\Domain\Group\Repository\GroupRepository;

class GroupsCount extends AbstractProgressbarWidget
{
    private GroupRepository $groupRepository;

    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    public function getTitle(): string
    {
        return 'zentlix_user.widgets.count_groups';
    }

    public function getText(): string
    {
        return 'zentlix_user.widgets.groups';
    }

    public function getHelpText(): string
    {
        return 'zentlix_user.widgets.groups_help';
    }

    public function getProgressbarPercent(): float
    {
        return 100;
    }

    public function getValue(): int
    {
        return $this->groupRepository->count([]);
    }

    public function getBackgroundGradient(): string
    {
        return '';
    }

    public function getProgressbarBackgroundGradient(): string
    {
        return self::GRADIENT_GREEN;
    }

    public function getColor(): string
    {
        return '#3c4b64';
    }

    public function getHelpTextColor(): string
    {
        return '#768192';
    }
}