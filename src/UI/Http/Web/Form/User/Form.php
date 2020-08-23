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

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zentlix\MainBundle\UI\Http\Web\FormType\AbstractForm;
use Zentlix\MainBundle\UI\Http\Web\Type;
use Zentlix\UserBundle\Domain\Group\Repository\GroupRepository;
use Zentlix\UserBundle\Domain\User\Entity\User;
use Zentlix\UserBundle\Domain\User\Specification\UniqueEmailSpecification;

class Form extends AbstractForm
{
    protected EventDispatcherInterface $eventDispatcher;
    protected TranslatorInterface $translator;
    protected GroupRepository $groupRepository;

    public function __construct(EventDispatcherInterface $eventDispatcher, TranslatorInterface $translator, GroupRepository $groupRepository)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->translator = $translator;
        $this->groupRepository = $groupRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            $builder->create('main', Type\FormType::class, ['inherit_data' => true, 'label' => 'zentlix_main.main'])
                ->add('email', Type\EmailType::class, [
                    'label' => 'zentlix_user.email',
                    'specification' => UniqueEmailSpecification::class
                ])
                ->add('groups', Type\ChoiceType::class, [
                    'choices'  => $this->groupRepository->assoc(),
                    'label' => 'zentlix_user.group.groups',
                    'multiple' => true
                ])
                ->add('status', Type\ChoiceType::class, [
                    'choices'  => [
                        $this->translator->trans('zentlix_user.active') => User::STATUS_ACTIVE,
                        $this->translator->trans('zentlix_user.wait_activation') => User::STATUS_WAIT,
                        $this->translator->trans('zentlix_user.blocked') => User::STATUS_BLOCKED
                    ],
                    'label' => 'zentlix_user.status'
                ])
                ->add('plain_password', Type\RepeatedType::class, [
                    'type' => Type\PasswordType::class,
                    'invalid_message' => $this->translator->trans('zentlix_user.validation.password_equal'),
                    'first_options'   => ['label' => $this->translator->trans('zentlix_user.password')],
                    'second_options'  => ['label' => $this->translator->trans('zentlix_user.password_confirm')],
                    'required'        => false
                ])
                ->add('first_name', Type\TextType::class, [
                    'label' => 'zentlix_user.first_name',
                    'required' => false
                ])
                ->add('last_name', Type\TextType::class, [
                    'label' => 'zentlix_user.last_name',
                    'required' => false
                ])
                ->add('middle_name', Type\TextType::class, [
                    'label' => 'zentlix_user.middle_name',
                    'required' => false
                ])
        );
        $builder->add(
            $builder->create('address', Type\FormType::class, ['inherit_data' => true, 'label' => 'zentlix_user.address'])
                ->add('phone', Type\PhoneNumberType::class, [
                    'label'    => 'zentlix_user.phone_number',
                    'required' => false
                ])
                ->add('zip', Type\TextType::class, [
                    'label'    => 'zentlix_user.zip',
                    'required' => false
                ])
                ->add('country', Type\TextType::class, [
                    'label' => 'zentlix_user.country',
                    'required' => false
                ])
                ->add('city', Type\TextType::class, [
                    'label'    => 'zentlix_user.city',
                    'required' => false
                ])
                ->add('street', Type\TextType::class, [
                    'label'    => 'zentlix_user.street',
                    'required' => false
                ])
                ->add('house', Type\TextType::class, [
                    'label'    => 'zentlix_user.house',
                    'required' => false
                ])
                ->add('flat', Type\TextType::class, [
                    'label'    => 'zentlix_user.flat',
                    'required' => false
                ])
        );
    }
}