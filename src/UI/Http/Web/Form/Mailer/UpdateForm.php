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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Zentlix\UserBundle\Application\Command\Mailer\Template\UpdateCommand;
use Zentlix\UserBundle\Domain\Mailer\Event\Template\UpdateForm as UpdateFormEvent;

class UpdateForm extends Form
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $this->eventDispatcher->dispatch(new UpdateFormEvent($builder));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'     => UpdateCommand::class,
            'label'          => 'zentlix_user.mailer.update.process',
            'form'           =>  self::SIMPLE_FORM,
            'deleteBtnLabel' => 'zentlix_user.mailer.delete.action',
            'deleteConfirm'  => 'zentlix_user.mailer.delete.confirm'
        ]);
    }
}