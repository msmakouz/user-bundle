<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\AdminSetting\Widgets;

use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandInterface;

class ChangeWidgetsCommand implements CommandInterface
{
    public function __construct(array $widgets)
    {
        foreach ($widgets as $class => $isVisible) {
            $this->__set($class, $isVisible);
        }
    }

    public function __get($widget): bool
    {
        if($this->__isset($widget)) {
            return $this->{str_replace(':', '\\', $widget)};
        }

        return false;
    }

    public function __set($widget, $isVisible): void
    {
        $this->{str_replace(':', '\\', $widget)} = $isVisible;
    }

    public function __isset($widget): bool
    {
        return isset($this->{str_replace(':', '\\', $widget)});
    }
}