<?php

declare(strict_types=1);

namespace Zentlix\UserBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Zentlix\UserBundle\Domain\Admin\Repository\SettingRepository;
use Zentlix\UserBundle\Domain\User\Entity\User;
use Zentlix\UserBundle\Domain\User\Event\AfterLogin;

class UserSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private SettingRepository $settingRepository,
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack
    ) {
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
                $this->requestStack->getSession()->set('_locale', $setting->getLocale()->getCode());
            }
        }
    }
}
