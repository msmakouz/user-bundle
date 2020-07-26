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

use Doctrine\ORM\NonUniqueResultException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zentlix\MainBundle\Domain\Shared\Specification\AbstractSpecification;
use Zentlix\UserBundle\Domain\Group\Repository\GroupRepository;

final class UniqueCodeSpecification extends AbstractSpecification
{
    private GroupRepository $groupRepository;
    private TranslatorInterface $translator;

    public function __construct(GroupRepository $groupRepository, TranslatorInterface $translator)
    {
        $this->groupRepository = $groupRepository;
        $this->translator = $translator;
    }

    public function isUnique(string $code): bool
    {
        return $this->isSatisfiedBy($code);
    }

    public function isSatisfiedBy($value): bool
    {
        if($this->groupRepository->findOneByCode($value) !== null) {
            throw new NonUniqueResultException(sprintf($this->translator->trans('zentlix_user.validation.group_exist'), $value));
        }

        return true;
    }
}