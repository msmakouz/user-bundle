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
use Zentlix\MainBundle\Application\Command\DynamicPropertyCommand;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandInterface;
use Zentlix\UserBundle\Domain\Mailer\Entity\Event;
use Zentlix\UserBundle\Domain\Mailer\Entity\Template;

class Command extends DynamicPropertyCommand implements CommandInterface
{
    /** @Constraints\NotBlank() */
    public ?string $title;
    public ?bool $active;
    public ?string $code;
    protected Template $entity;

    /**
     * @var int|Event
     * @Constraints\NotBlank()
     */
    public $event;

    /**
     * @var string
     * @Constraints\NotBlank()
     */
    public $provider;
    public $provider_title;

    /** @Constraints\NotBlank() */
    public ?string $theme;

    /** @Constraints\NotBlank() */
    public ?string $body;

    /** @Constraints\NotBlank() */
    public ?string $recipient;

    /** @Constraints\NotBlank() */
    public ?array $sites = [];

    public function getEntity(): Template
    {
        return $this->entity;
    }
}