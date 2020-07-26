<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Admin\Event\Setting;

final class AfterChangeWidgets
{
    private array $widgets;

    public function __construct(array $widgets)
    {
        $this->widgets = $widgets;
    }
}