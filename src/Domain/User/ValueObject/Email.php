<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\User\ValueObject;

class Email
{
    private $value;

    public function __construct(string $value)
    {
        if (empty($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Incorrect Email.');
        }
        $this->value = mb_strtolower($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isEqual(self $other): bool
    {
        return $this->getValue() === $other->getValue();
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}