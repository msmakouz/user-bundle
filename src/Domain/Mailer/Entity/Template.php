<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Mailer\Entity;

use Doctrine\ORM\Mapping;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation\Slug;
use Zentlix\MainBundle\Domain\Shared\Entity\Eventable;
use Zentlix\UserBundle\Application\Command\Mailer\Template\CreateCommand;
use Zentlix\UserBundle\Application\Command\Mailer\Template\UpdateCommand;

/**
 * @Mapping\Entity(repositoryClass="Zentlix\UserBundle\Domain\Mailer\Repository\TemplateRepository")
 * @Mapping\Table(name="zx_mailer_templates", uniqueConstraints={
 *     @Mapping\UniqueConstraint(columns={"code"})
 * })
 */
class Template implements Eventable
{
    /**
     * @Mapping\Id()
     * @Mapping\GeneratedValue()
     * @Mapping\Column(type="integer")
     */
    private $id;

    /** @Mapping\Column(type="string", length=255) */
    private $title;

    /** @Mapping\Column(type="boolean", options={"default": "1"}) */
    private $active;

    /**
     * @Slug(fields={"title"}, updatable=false)
     * @Mapping\Column(type="string", length=64, unique=true)
     */
    private $code;

    /** @Mapping\Column(type="string", length=255) */
    private $theme;

    /** @Mapping\Column(type="text") */
    private $body;

    /** @Mapping\Column(type="string", length=255, nullable=true) */
    private $recipient;

    /**
     * @var Event
     * @Mapping\ManyToOne(targetEntity="Event")
     * @Mapping\JoinColumn(name="event_id", referencedColumnName="id", nullable=false)
     */
    private $event;

    /** @Mapping\Column(type="string", length=64) */
    private $provider;

    /** @Mapping\Column(type="string", length=64) */
    private $provider_title;

    /**
     * @Mapping\ManyToMany(targetEntity="Zentlix\MainBundle\Domain\Site\Entity\Site")
     * @Mapping\JoinTable(name="zx_site_mailer_templates")
     */
    private $sites;

    public function __construct(CreateCommand $command)
    {
        $this->sites = new ArrayCollection();

        $this->setValuesFromCommands($command);
    }

    public function update(UpdateCommand $command)
    {
        $this->setValuesFromCommands($command);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTheme(): string
    {
        return $this->theme;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getRecipient(): string
    {
        return $this->recipient;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getProviderTitle(): ?string
    {
        return $this->provider_title;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getSites()
    {
        return $this->sites;
    }

    public function isCodeEqual(string $code): bool
    {
        return $code === $this->code;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param CreateCommand|UpdateCommand $command
     */
    private function setValuesFromCommands($command): void
    {
        $this->title = $command->title;
        $this->active = $command->active;
        $this->provider = $command->provider;
        $this->provider_title = $command->provider_title;
        $this->code = $command->code;
        $this->theme = $command->theme;
        $this->body = $command->body;
        $this->recipient = $command->recipient;
        $this->event = $command->event;
        $this->sites = $command->sites;
    }
}