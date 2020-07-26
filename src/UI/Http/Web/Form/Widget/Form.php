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
use Symfony\Contracts\Translation\TranslatorInterface;
use Zentlix\MainBundle\UI\Http\Web\FormType\AbstractForm;
use Zentlix\MainBundle\UI\Http\Web\Type;
use Zentlix\UserBundle\Application\Command\AdminSetting\Widgets\ChangeWidgetsCommand;
use Zentlix\UserBundle\Domain\Admin\Event\Setting\WidgetsForm;

class Form extends AbstractForm
{
    protected EventDispatcherInterface $eventDispatcher;
    protected TranslatorInterface $translator;

    public function __construct(EventDispatcherInterface $eventDispatcher, TranslatorInterface $translator)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var ChangeWidgetsCommand $command */
        $command = $builder->getData();
        $main = $builder->create('main', Type\FormType::class, ['inherit_data' => true, 'label' => 'zentlix_main.main']);

        foreach ($command->availableWidgets as $widget) {
            $reflection = new \ReflectionClass($widget);
            $main->add(str_replace('\\', ':', $reflection->getName()), Type\CheckboxType::class, [
                'label' => ChangeWidgetsCommand::$widgetsTitles[$reflection->getName()],
                'required' => false
            ]);
        }

        $builder->add($main);

        $this->eventDispatcher->dispatch(new WidgetsForm($builder));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label'      => 'zentlix_user.widgets.update',
            'data_class' => ChangeWidgetsCommand::class,
            'form'       => self::SIMPLE_FORM
        ]);
    }
}