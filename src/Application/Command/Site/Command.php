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

use Symfony\Component\Validator\Constraints;
use Zentlix\MainBundle\Application\Command\DynamicPropertyCommand;
use Zentlix\MainBundle\Application\Command\EmailTrait;
use Zentlix\MainBundle\Domain\Site\Entity\Site as SiteEntity;
use Zentlix\MainBundle\Infrastructure\Share\Bus\CommandInterface;
use Zentlix\UserBundle\Domain\User\Entity\Site;

class Command extends DynamicPropertyCommand implements CommandInterface
{
    use EmailTrait;

    /**
     * @Constraints\NotBlank()
     * @Constraints\Email()
     */
    public ?string $email = null;

    public $id;
    public ?string $smtp_host = null;
    public ?int $smtp_port = null;
    public ?string $smtp_user = null;
    public ?string $smtp_password = null;
    public SiteEntity $site;
    protected Site $entity;

    public function getEntity(): Site
    {
        return $this->entity;
    }
}