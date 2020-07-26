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
use Zentlix\MainBundle\UI\Http\Web\Type\IntegerType;
use Zentlix\UserBundle\Application\Command\Group\CreateCommand;
use Zentlix\UserBundle\Domain\Group\Event\CreateForm as CreateFormEvent;

class CreateForm extends Form
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $sort = $builder->get('main')->get('sort')->getOptions();
        $builder->get('main')->add('sort', IntegerType::class, array_replace($sort, ['data' => $this->groupRepository->getMaxSort() + 1]));

        $this->eventDispatcher->dispatch(new CreateFormEvent($builder));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label'      => 'zentlix_user.group.create.process',
            'data_class' => CreateCommand::class,
            'form'       => self::SIMPLE_FORM
        ]);
    }
}