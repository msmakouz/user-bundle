<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\UI\Http\Web\Form\User;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zentlix\UserBundle\Application\Command\User\CreateCommand;
use Zentlix\UserBundle\Domain\User\Event\RegistrationForm as RegistrationFormEvent;
use Zentlix\MainBundle\UI\Http\Web\Type;
use Zentlix\MainBundle\UI\Http\Web\FormType\AbstractForm;

class RegistrationForm extends AbstractForm
{
    private TranslatorInterface $translator;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher, TranslatorInterface $translator)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', Type\EmailType::class, ['label' => 'zentlix_user.email'])
            ->add('plain_password', Type\PasswordType::class, [
                'label' => 'zentlix_user.password',
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('zentlix_user.validation.enter_password'),
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => $this->translator->trans('zentlix_user.validation.password_length'),
                        'max' => 4096,
                    ]),
                ],
                'invalid_message' => $this->translator->trans('zentlix_user.validation.password_equal')
            ]);

        $this->eventDispatcher->dispatch(new RegistrationFormEvent($builder));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CreateCommand::class,
        ]);
    }
}