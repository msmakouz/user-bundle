<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Group\Specification;

use Symfony\Contracts\Translation\TranslatorInterface;
use Zentlix\MainBundle\Application\Query\NotFoundException;
use Zentlix\MainBundle\Domain\Shared\Specification\AbstractSpecification;
use Zentlix\UserBundle\Domain\Group\Repository\GroupRepository;

final class ExistGroupByCodeSpecification extends AbstractSpecification
{
    private GroupRepository $groupRepository;
    private TranslatorInterface $translator;

    public function __construct(GroupRepository $groupRepository, TranslatorInterface $translator)
    {
        $this->groupRepository = $groupRepository;
        $this->translator = $translator;
    }

    public function isExist(string $code): bool
    {
        return $this->isSatisfiedBy($code);
    }

    public function isSatisfiedBy($value): bool
    {
        if($this->groupRepository->hasByCode($value) === false) {
            throw new NotFoundException(sprintf($this->translator->trans('zentlix_user.validation.group_not_exist'), $value));
        }

        return true;
    }

    public function __invoke(string $code): bool
    {
        return $this->isExist($code);
    }
}