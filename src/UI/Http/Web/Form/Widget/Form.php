<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\UI\Http\Web\Form\Widget;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zentlix\MainBundle\Domain\Dashboard\Service\Widgets;
use Zentlix\MainBundle\UI\Http\Web\FormType\AbstractForm;
use Zentlix\MainBundle\UI\Http\Web\Type;
use Zentlix\UserBundle\Application\Command\AdminSetting\Widgets\ChangeWidgetsCommand;
use Zentlix\UserBundle\Domain\Admin\Event\Setting\WidgetsForm;

class Form extends AbstractForm
{
    private EventDispatcherInterface $eventDispatcher;
    private Widgets $widgets;

    public function __construct(EventDispatcherInterface $eventDispatcher, Widgets $widgets)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->widgets = $widgets;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($this->widgets->getWidgets() as $class => $widget) {
            $builder->add(str_replace('\\', ':', $class), Type\CheckboxType::class, [
                'label'    => $widget->getTitle(),
                'required' => false
            ]);
        }

        $this->eventDispatcher->dispatch(new WidgetsForm($builder));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ChangeWidgetsCommand::class]);
    }
}