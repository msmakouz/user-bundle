<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\User\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Zentlix\MainBundle\Application\Query\NotFoundException;
use Zentlix\UserBundle\Domain\User\Entity\Site;

/**
 * @method Site|null find($id, $lockMode = null, $lockVersion = null)
 * @method Site|null findOneBy(array $criteria, array $orderBy = null)
 * @method Site[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Site::class);
    }

    public function getOneBySiteId($siteId): Site
    {
        $userSite = $this->createQueryBuilder('u')
            ->andWhere('u.site = :val')
            ->setParameter('val', $siteId)
            ->getQuery()
            ->getOneOrNullResult();

        if(!$userSite) {
            throw new NotFoundException(sprintf('User site settings not found for site id %s.', $siteId));
        }

        return $userSite;
    }

    public function findOneBySiteId($siteId): Site
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.site = :val')
            ->setParameter('val', $siteId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}