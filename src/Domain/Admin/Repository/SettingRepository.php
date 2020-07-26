<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Admin\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Zentlix\UserBundle\Domain\Admin\Entity\Setting;
use Zentlix\MainBundle\Domain\Shared\Repository\GetTrait;

/**
 * @method Setting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Setting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Setting      get($id, $lockMode = null, $lockVersion = null)
 * @method Setting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingRepository extends ServiceEntityRepository
{
    use GetTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Setting::class);
    }

    public function findByUserId(int $userId): ?Setting
    {
        return $this->findOneBy(['user' => $userId]);
    }
}