<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Zentlix\UserBundle\Domain\Admin\Repository\SettingRepository;
use Zentlix\UserBundle\Domain\User\Entity\User;
use Zentlix\UserBundle\Domain\User\Event\AfterLogin;

class UserSubscriber implements EventSubscriberInterface
{
    private SettingRepository $settingRepository;
    private EntityManagerInterface $entityManager;
    private SessionInterface $session;

    public function __construct(SettingRepository $settingRepository, EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->settingRepository = $settingRepository;
        $this->entityManager = $entityManager;
        $this->session = $session;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterLogin::class => 'onAfterLogin',
        ];
    }

    public function onAfterLogin(AfterLogin $afterLogin)
    {
        /** @var User $user */
        $user = $afterLogin->getUser();

        $user->setLastLogin(new \DateTimeImmutable());
        $this->entityManager->flush();

        if($user->isAdminRole()) {
            $setting = $this->settingRepository->findByUserId($user->getId());
            if($setting) {
                $this->session->set('_locale', $setting->getLocale()->getCode());
            }
        }
    }
}