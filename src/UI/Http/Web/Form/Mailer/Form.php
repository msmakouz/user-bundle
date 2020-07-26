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
use Symfony\Contracts\Translation\TranslatorInterface;
use Zentlix\MainBundle\Domain\Site\Repository\SiteRepository;
use Zentlix\MainBundle\UI\Http\Web\FormType\AbstractForm;
use Zentlix\MainBundle\UI\Http\Web\Type;
use Zentlix\UserBundle\Application\Command\Mailer\Template\Command;
use Zentlix\UserBundle\Domain\Mailer\Repository\EventRepository;
use Zentlix\UserBundle\Domain\Mailer\Service\Providers;

class Form extends AbstractForm
{
    protected EventDispatcherInterface $eventDispatcher;
    protected TranslatorInterface $translator;
    protected EventRepository $eventRepository;
    protected Providers $providers;
    protected SiteRepository $siteRepository;

    public function __construct(EventDispatcherInterface $eventDispatcher,
                                TranslatorInterface $translator,
                                EventRepository $eventRepository,
                                Providers $providers,
                                SiteRepository $siteRepository)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->translator = $translator;
        $this->eventRepository = $eventRepository;
        $this->providers = $providers;
        $this->siteRepository = $siteRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Command $command */
        $command = $builder->getData();

        $builder->add('title', Type\TextType::class, ['label' => 'zentlix_main.title'])
            ->add('event', Type\ChoiceType::class, [
                'choices' => $this->eventRepository->assoc(),
                'label' => 'zentlix_user.mailer.event',
                'update' => true
            ])
            ->add('provider', Type\ChoiceType::class, [
                'choices' => $this->providers->assoc(),
                'label' => 'zentlix_user.mailer.provider'
            ])
            ->add('active', Type\CheckboxType::class, ['label' => 'zentlix_user.mailer.active']);

        $builder->add('sites', Type\ChoiceType::class, [
            'choices' => $this->siteRepository->assoc(),
            'label' => 'zentlix_main.site.sites',
            'multiple' => true
        ])
            ->add('recipient', Type\TextType::class, [
                'label' => 'zentlix_user.mailer.recipient',
                'help' => 'zentlix_user.mailer.recipient_hint'
            ])
            ->add('code', Type\TextType::class, ['label' => 'zentlix_main.symbol_code', 'required' => false])
            ->add('theme', Type\TextType::class, ['label' => 'zentlix_main.theme']);
        if ($command->event > 0) {
            $event = $this->eventRepository->get($command->event);
            $data = $event->getFormattedAvailableData($this->translator);
            if ($data) {
                $builder->add('available_data', Type\NoticeType::class, ['data' => $data]);
            }
        }
        $builder->add('body', Type\EditorType::class, ['required' => true, 'label' => 'zentlix_user.mailer.body']);
    }
}