<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Mailer\Event;

use Zentlix\UserBundle\Domain\Mailer\Entity\Template;

final class BeforeSend
{
    private Template $template;
    private array $data;

    public function __construct(Template $template, array &$data)
    {
        $this->template = $template;
        $this->data = $data;
    }

    public function getTemplate(): Template
    {
        return $this->template;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
