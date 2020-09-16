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

use Doctrine\ORM\NonUniqueResultException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Zentlix\UserBundle\Domain\User\Repository\UserRepository;
use Zentlix\UserBundle\Domain\User\ValueObject\Email;
use function is_null;

final class UniqueEmailSpecification
{
    private UserRepository $userRepository;
    private TranslatorInterface $translator;

    public function __construct(UserRepository $userRepository, TranslatorInterface $translator)
    {
        $this->userRepository = $userRepository;
        $this->translator = $translator;
    }

    public function isUnique(Email $email): void
    {
        if(is_null($this->userRepository->findOneByEmail($email)) === false) {
            throw new NonUniqueResultException(sprintf($this->translator->trans('zentlix_user.validation.user_email_unique'), $email->getValue()));
        }
    }

    public function __invoke(Email $email): void
    {
        $this->isUnique($email);
    }
}
