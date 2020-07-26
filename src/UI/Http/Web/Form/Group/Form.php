<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\UI\Http\Web\Form\Group;

use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zentlix\MainBundle\UI\Http\Web\FormType\AbstractForm;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;
use Zentlix\UserBundle\Domain\Group\Repository\GroupRepository;

class Form extends AbstractForm
{
    protected EventDispatcherInterface $eventDispatcher;
    protected TranslatorInterface $translator;
    protected ?UserInterface $user;
    protected GroupRepository $groupRepository;

    public function __construct(EventDispatcherInterface $eventDispatcher,
                                TranslatorInterface $translator,
                                Security $security,
                                GroupRepository $groupRepository)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->translator = $translator;
        $this->groupRepository = $groupRepository;
        $this->user = $security->getUser();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            $builder->create('main', Type\FormType::class, ['inherit_data' => true, 'label' => 'zentlix_main.main'])
                ->add('title', Type\TextType::class, ['label' => 'zentlix_main.title'])
                ->add('code', Type\TextType::class, ['label' => 'zentlix_main.symbol_code', 'required' => false])
                ->add('group_role', Type\ChoiceType::class, [
                    'choices'  => [
                        'zentlix_user.role_user'  => UserGroup::GROUP_ROLE_USER,
                        'zentlix_user.role_admin' => UserGroup::GROUP_ROLE_ADMIN
                    ],
                    'label' => 'zentlix_user.group_access'
                ])
                ->add('sort', Type\IntegerType::class, [
                    'label' => 'zentlix_main.sort',
                    'constraints' => [
                        new GreaterThan(['value' => 0, 'message' => $this->translator->trans('zentlix_main.validation.greater_0')])
                    ]
                ])
        );
    }
}