<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Admin\Specification;

use Symfony\Contracts\Translation\TranslatorInterface;

final class ExistWidgetSpecification
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function isExist(string $class): void
    {
        if(class_exists($class) === false) {
            throw new \Exception(sprintf($this->translator->trans('zentlix_user.validation.widget_not_exist'), $class));
        }
    }

    public function __invoke(string $class): void
    {
        $this->isExist($class);
    }
}