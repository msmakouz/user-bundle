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
use Zentlix\MainBundle\Domain\Shared\Entity\Eventable;
use Zentlix\MainBundle\Domain\Site\Entity\Site as SiteEntity;
use Zentlix\MainBundle\Infrastructure\Share\Doctrine\UuidInterface;
use Zentlix\UserBundle\Application\Command\Site\CreateCommand;
use Zentlix\UserBundle\Application\Command\Site\UpdateCommand;
use Zentlix\UserBundle\Domain\User\ValueObject\Email;

/**
 * @Mapping\Entity(repositoryClass="Zentlix\UserBundle\Domain\User\Repository\SiteRepository")
 * @Mapping\Table(name="zx_user_site")
 */
class Site implements Eventable
{
    /**
     * @var UuidInterface
     * @Mapping\Id
     * @Mapping\Column(type="uuid_binary")
     */
    private $id;

    /**
     * @var Email
     * @Mapping\Column(type="email", length=180)
     */
    private $email;

    /** @Mapping\Column(type="string", length=64, nullable=true) */
    private $smtp_host;

    /** @Mapping\Column(type="integer", options={"default": "0"}) */
    private $smtp_port;

    /** @Mapping\Column(type="string", length=255, nullable=true) */
    private $smtp_user;

    /** @Mapping\Column(type="string", length=255, nullable=true) */
    private $smtp_password;

    /**
     * @var SiteEntity
     * @Mapping\ManyToOne(targetEntity="Zentlix\MainBundle\Domain\Site\Entity\Site")
     * @Mapping\JoinColumn(name="site_id", referencedColumnName="id", nullable=false)
     */
    private $site;

    public function __construct(CreateCommand $command)
    {
        $this->id = $command->id;

        $this->setValuesFromCommands($command);
    }

    public function update(UpdateCommand $command)
    {
        $this->setValuesFromCommands($command);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getSmtpHost(): ?string
    {
        return $this->smtp_host;
    }

    public function getSmtpPort(): int
    {
        return $this->smtp_port ?? 0;
    }

    public function getSmtpUser(): ?string
    {
        return $this->smtp_user;
    }

    public function getSmtpPassword(): ?string
    {
        return $this->smtp_password;
    }

    public function getSite(): SiteEntity
    {
        return $this->site;
    }

    /**
     * @param CreateCommand|UpdateCommand $command $command
     */
    private function setValuesFromCommands($command): void
    {
        $this->email = $command->getEmailObject();
        $this->smtp_host = $command->smtp_host;
        $this->smtp_port = $command->smtp_port;
        $this->smtp_user = $command->smtp_user;
        $this->smtp_password = $command->smtp_password;
        $this->site = $command->site;
    }
}