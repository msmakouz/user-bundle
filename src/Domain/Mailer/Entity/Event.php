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
use Symfony\Contracts\Translation\TranslatorInterface;
use Gedmo\Mapping\Annotation\Slug;
use Zentlix\MainBundle\Domain\Bundle\Entity\Bundle;

/**
 * @Mapping\Entity(repositoryClass="Zentlix\UserBundle\Domain\Mailer\Repository\EventRepository")
 * @Mapping\Table(name="zentlix_user_mailer_events", uniqueConstraints={
 *     @Mapping\UniqueConstraint(columns={"code"})
 * })
 */
class Event
{
    /**
     * @Mapping\Id()
     * @Mapping\GeneratedValue()
     * @Mapping\Column(type="integer")
     */
    private $id;

    /** @Mapping\Column(type="string", length=255) */
    private $title;

    /**
     * @Slug(fields={"title"}, updatable=false)
     * @Mapping\Column(type="string", length=64, unique=true)
     */
    private $code;

    /** @Mapping\Column(type="json", nullable=true) */
    private $available_data;

    /**
     * @var Bundle
     * @Mapping\ManyToOne(targetEntity="Zentlix\MainBundle\Domain\Bundle\Entity\Bundle")
     * @Mapping\JoinColumn(name="bundle_id", referencedColumnName="id", nullable=false)
     */
    private $bundle;

    public function __construct(string $title, array $available_data = [], string $code = null)
    {
        $this->title = $title;
        $this->available_data = $available_data;
        $this->code = $code;
    }

    public function setBundle(Bundle $bundle): self
    {
        $this->bundle = $bundle;

        return $this;
    }

    public function setAvailableData(array $availableData): self
    {
        $this->available_data = $availableData;

        return $this;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getAvailableData(): array
    {
        return \is_array($this->available_data) ? $this->available_data : [];
    }

    public function getFormattedAvailableData(TranslatorInterface $translator)
    {
        if(\count($this->getAvailableData()) === 0) {
            return null;
        }

        $result = '';
        foreach ($this->getAvailableData() as $data => $title) {
            $result .= $translator->trans($title) . ': ' . '#' . $data . '#<br>';
        }

        return $result;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}