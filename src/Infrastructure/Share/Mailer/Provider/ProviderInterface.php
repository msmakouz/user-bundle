<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Infrastructure\Share\Mailer\Provider;

use Zentlix\UserBundle\Domain\Mailer\Entity\Template;

interface ProviderInterface
{
    public function getCode(): string;

    public function getTitle(): string;

    public function send(Template $template, string $defaultTo, array $data = []): void;
}