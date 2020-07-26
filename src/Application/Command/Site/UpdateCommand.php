<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Application\Command\Site;

use Zentlix\MainBundle\Application\Command\UpdateCommandInterface;
use Zentlix\UserBundle\Domain\User\Entity\Site;

class UpdateCommand extends Command implements UpdateCommandInterface
{
    public function __construct(Site $site)
    {
        $this->site = $site->getSite();

        $this->entity = $site;
    }
}