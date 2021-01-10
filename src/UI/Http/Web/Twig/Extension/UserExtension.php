<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\UI\Http\Web\Twig\Extension;

use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function is_null;

class UserExtension extends AbstractExtension
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_admin', function () {
                /** @var \Zentlix\UserBundle\Domain\User\Entity\User $user */
                $user = $this->security->getUser();

                if(is_null($user)) {
                    return false;
                }

                return $user->isAdminRole();

            }, ['needs_environment' => false]),
        ];
    }
}