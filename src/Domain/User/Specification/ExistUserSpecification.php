<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\User\Specification;

use Symfony\Contracts\Translation\TranslatorInterface;
use Zentlix\MainBundle\Infrastructure\Share\Bus\NotFoundException;
use Zentlix\UserBundle\Domain\User\Repository\UserRepository;
use function is_null;

final class ExistUserSpecification
{
    private UserRepository $userRepository;
    private TranslatorInterface $translator;

    public function __construct(UserRepository $userRepository, TranslatorInterface $translator)
    {
        $this->userRepository = $userRepository;
        $this->translator = $translator;
    }

    public function isExist(int $userId): void
    {
        if(is_null($this->userRepository->find($userId))) {
            throw new NotFoundException($this->translator->trans('zentlix_user.validation.user_not_exist'), $userId);
        }
    }

    public function __invoke(int $userId): void
    {
        $this->isExist($userId);
    }
}