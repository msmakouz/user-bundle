<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Admin\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Zentlix\MainBundle\Domain\Locale\Entity\Locale;
use Zentlix\MainBundle\Domain\Setting\Service\Settings;
use Zentlix\UserBundle\Domain\Admin\Entity\Setting;
use Zentlix\UserBundle\Domain\User\Entity\User;
use function is_null;

class AdminSettings
{
    private const DEFAULT_WIDGETS = [
        'Zentlix\\MainBundle\\UI\\Http\\Web\\DashboardWidget\\SitesCount'  => true,
        'Zentlix\\UserBundle\\UI\\Http\\Web\\DashboardWidget\\UsersCount'  => true,
        'Zentlix\\UserBundle\\UI\\Http\\Web\\DashboardWidget\\GroupsCount' => true,
    ];

    private EntityManagerInterface $entityManager;
    private ?int $userId = null;
    private Settings $settings;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager, Settings $settings)
    {
        $token = $tokenStorage->getToken();

        if(is_null($token) === false && $token->getUser() instanceof UserInterface) {
            $this->userId = $token->getUser()->getId();
        }

        $this->entityManager = $entityManager;
        $this->settings = $settings;
    }

    public function getLocale(): Locale
    {
        return $this->getSettings()->getLocale();
    }

    public function getWidgets(): array
    {
        return $this->getSettings()->getWidgets();
    }

    public function getSettings(): Setting
    {
        if(is_null($this->userId)) {
            throw new \DomainException('User not found');
        }

        $adminSettingRepository = $this->entityManager->getRepository(Setting::class);
        $settings = $adminSettingRepository->findByUserId($this->userId);

        if(is_null($settings)) {
            $settings = $this->createSettings();
        }

        return $settings;
    }

    private function createSettings(): Setting
    {
        $userRepository = $this->entityManager->getRepository(User::class);

        $settings = new Setting(
            $this->settings->getDefaultLocale(),
            self::DEFAULT_WIDGETS,
            $userRepository->get($this->userId));

        $this->entityManager->persist($settings);
        $this->entityManager->flush();

        return $settings;
    }
}