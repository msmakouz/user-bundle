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
use Zentlix\MainBundle\Domain\Shared\Specification\AbstractSpecification;

final class ExistWidgetSpecification extends AbstractSpecification
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function isExist(string $class): bool
    {
        return $this->isSatisfiedBy($class);
    }

    public function isSatisfiedBy($value): bool
    {
        if(class_exists($value) === false) {
            throw new \Exception(sprintf($this->translator->trans('zentlix_user.validation.widget_not_exist'), $value));
        }

        return true;
    }

    public function __invoke(string $class): bool
    {
        return $this->isExist($class);
    }
}