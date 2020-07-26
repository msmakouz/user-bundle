<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\User\Entity;

use Doctrine\ORM\Mapping;

/**
 * @Mapping\Embeddable
 */
class ResetToken
{
    /** @Mapping\Column(type="string", nullable=true) */
    private $token;

    /** @Mapping\Column(type="datetime_immutable", nullable=true) */
    private $expires;

    public function __construct(string $token, \DateTimeImmutable $expires)
    {
        $this->token = $token;
        $this->expires = $expires;
    }

    public function isExpiredTo(\DateTimeImmutable $date): bool
    {
        return $this->expires <= $date;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @internal for postLoad callback
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->token);
    }
}
