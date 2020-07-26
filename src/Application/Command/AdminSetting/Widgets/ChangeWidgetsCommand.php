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

use Symfony\Component\Validator\Constraints;
use Zentlix\MainBundle\Application\Command\DynamicPropertyCommand;
use Zentlix\MainBundle\Domain\Dashboard\WidgetInterface;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandInterface;

class ChangeWidgetsCommand extends DynamicPropertyCommand implements CommandInterface
{
    /** @Constraints\NotBlank() */
    public array $availableWidgets;
    public array $widgets;

    public static array $widgetsTitles;

    public function __construct(array $widgets, array $availableWidgets)
    {
        $this->availableWidgets = $availableWidgets;

        /** @var WidgetInterface $widget */
        foreach ($availableWidgets as $widget) {
            $reflection = new \ReflectionClass($widget);
            self::$widgetsTitles[$reflection->getName()] = $widget->getTitle();
            $this->createProperty(str_replace('\\', ':', $reflection->getName()),
                isset($widgets[$reflection->getName()]) ? $widgets[$reflection->getName()] : false);
        }
    }
}