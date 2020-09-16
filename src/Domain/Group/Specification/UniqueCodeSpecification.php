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
use Zentlix\UserBundle\Domain\Group\Repository\GroupRepository;

final class UniqueCodeSpecification
{
    private GroupRepository $groupRepository;
    private TranslatorInterface $translator;

    public function __construct(GroupRepository $groupRepository, TranslatorInterface $translator)
    {
        $this->groupRepository = $groupRepository;
        $this->translator = $translator;
    }

    public function isUnique(string $code): void
    {
        if($this->groupRepository->hasByCode($code)) {
            throw new NonUniqueResultException(sprintf($this->translator->trans('zentlix_user.validation.group_exist'), $code));
        }
    }

    public function __invoke(string $code): void
    {
        $this->isUnique($code);
    }
}