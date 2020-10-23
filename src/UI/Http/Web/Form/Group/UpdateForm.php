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

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zentlix\MainBundle\UI\Http\Web\Type;
use Zentlix\UserBundle\Application\Command\Group\UpdateCommand;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;
use Zentlix\UserBundle\Domain\Group\Event\UpdateForm as UpdateFormEvent;

class UpdateForm extends Form
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $group = $builder->getData()->getEntity();

        $code = $builder->get('main')->get('code')->getOptions();
        $role = $builder->get('main')->get('group_role')->getOptions();

        $builder->get('main')->add('code', Type\TextType::class, array_replace($code, [
            'required' => true,
            'disabled' => $group->isSystemGroup($group->getCode())
        ]));
        $builder->get('main')->add('group_role', Type\ChoiceType::class, array_replace($role, [
            'disabled' => $group->isSystemGroup($group->getCode())
        ]));
        if($group->isRoleAdmin()) {
            $rights = $builder->create('rights', Type\FormType::class, [
                'label'        => 'zentlix_user.admin_rights',
                'required'     => false,
                'inherit_data' => true
            ]);

            foreach ($this->bundles->getBundles() as $class => $bundle) {
                $bundleRights = $builder->create(str_replace('\\', '_', $class), Type\FormType::class, [
                    'label'        => $bundle->getTitle(),
                    'required'     => false,
                    'inherit_data' => true,
                    'attr' => ['class' => 'bundle-rights']
                ]);
                foreach ($bundle->configureRights() as $right => $rightsTitle) {
                    $bundleRights->add(str_replace('\\', ':', $right), Type\CheckboxType::class, [
                        'label'    => $rightsTitle,
                        'required' => false,
                        'disabled' => $group->getCode() === UserGroup::ADMIN_GROUP || !$this->user->isAdminGroup(),
                        'data'     => $group->getCode() === UserGroup::ADMIN_GROUP ? true : $builder->getData()->{str_replace('\\', ':', $right)}
                    ]);
                }
                $rights->add($bundleRights);
            }

            $builder->add($rights);
        }

        $this->eventDispatcher->dispatch(new UpdateFormEvent($builder));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => UpdateCommand::class]);
    }
}