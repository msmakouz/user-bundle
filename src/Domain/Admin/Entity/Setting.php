<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Admin\Entity;

use Doctrine\ORM\Mapping;
use Zentlix\MainBundle\Domain\Locale\Entity\Locale;
use Zentlix\UserBundle\Domain\User\Entity\User;

/**
 * @Mapping\Entity(repositoryClass="Zentlix\UserBundle\Domain\Admin\Repository\SettingRepository")
 * @Mapping\Table(name="zentlix_user_admin_settings")
 */
class Setting
{
    /**
     * @Mapping\Id()
     * @Mapping\GeneratedValue()
     * @Mapping\Column(type="integer")
     */
    private $id;

    /**
     * @var Locale
     * @Mapping\ManyToOne(targetEntity="Zentlix\MainBundle\Domain\Locale\Entity\Locale")
     * @Mapping\JoinColumn(name="locale_id", referencedColumnName="id")
     */
    private $locale;

    /** @Mapping\Column(type="json") */
    private $widgets = [];

    /**
     * @var User
     * @Mapping\ManyToOne(targetEntity="Zentlix\UserBundle\Domain\User\Entity\User")
     * @Mapping\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function __construct(Locale $locale, array $widgets, User $user)
    {
        $this->locale = $locale;
        $this->widgets = $widgets;
        $this->user = $user;
    }

    public function setLocale(Locale $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    public function setWidgets(array $widgets): self
    {
        $this->widgets = $widgets;

        return $this;
    }

    public function getLocale(): Locale
    {
        return $this->locale;
    }

    public function getWidgets(): array
    {
        return $this->widgets ?? [];
    }
}