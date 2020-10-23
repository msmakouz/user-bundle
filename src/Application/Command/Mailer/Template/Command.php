<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\Mailer\Template;

use Symfony\Component\Validator\Constraints;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandInterface;
use Zentlix\UserBundle\Domain\Mailer\Entity\Template;

class Command implements CommandInterface
{
    /** @Constraints\NotBlank() */
    public ?string $title;
    public ?bool $active = true;
    public ?string $code = null;
    protected Template $entity;

    /** @Constraints\NotBlank() */
    public string $event;

    /** @Constraints\NotBlank() */
    public string $provider;

    /** @Constraints\NotBlank() */
    public ?string $theme;

    public ?string $body = null;

    /** @Constraints\NotBlank() */
    public ?string $recipient = '%default_to%';

    /** @Constraints\NotBlank() */
    public ?array $sites = [];

    public function getEntity(): Template
    {
        return $this->entity;
    }
}