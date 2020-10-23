<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\UI\Http\Web\Form\Mailer;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zentlix\MainBundle\Domain\Site\Repository\SiteRepository;
use Zentlix\MainBundle\UI\Http\Web\FormType\AbstractForm;
use Zentlix\MainBundle\UI\Http\Web\Type;
use Zentlix\UserBundle\Domain\Mailer\Service\Events;
use Zentlix\UserBundle\Domain\Mailer\Service\Providers;

class Form extends AbstractForm
{
    protected EventDispatcherInterface $eventDispatcher;
    protected Events $events;
    protected Providers $providers;
    protected SiteRepository $siteRepository;

    public function __construct(EventDispatcherInterface $eventDispatcher,
                                Events $events,
                                Providers $providers,
                                SiteRepository $siteRepository)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->events = $events;
        $this->providers = $providers;
        $this->siteRepository = $siteRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('title', Type\TextType::class, ['label' => 'zentlix_main.title'])
            ->add('event', Type\ChoiceType::class, [
                'choices' => $this->events->assoc(),
                'label'   => 'zentlix_user.mailer.event',
            ])
            ->add('provider', Type\ChoiceType::class, [
                'choices' => $this->providers->assoc(),
                'label'   => 'zentlix_user.mailer.provider'
            ])
            ->add('active', Type\CheckboxType::class, ['label' => 'zentlix_user.mailer.active'])
            ->add('sites', Type\ChoiceType::class, [
                'choices'  => $this->siteRepository->assoc(),
                'label'    => 'zentlix_main.site.sites',
                'multiple' => true
            ])
            ->add('recipient', Type\TextType::class, [
                'label' => 'zentlix_user.mailer.recipient',
                'help'  => 'zentlix_user.mailer.recipient_hint'
            ])
            ->add('code', Type\TextType::class, ['label' => 'zentlix_main.symbol_code', 'required' => false])
            ->add('theme', Type\TextType::class, ['label' => 'zentlix_main.theme'])
            ->add('body', Type\EditorType::class, ['label' => 'zentlix_user.mailer.body']);
    }
}