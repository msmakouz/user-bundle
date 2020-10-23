<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Mailer\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Zentlix\MainBundle\Domain\Shared\Repository\CodeTrait;
use Zentlix\UserBundle\Domain\Mailer\Entity\Template;

/**
 * @method Template|null find($id, $lockMode = null, $lockVersion = null)
 * @method Template|null findOneBy(array $criteria, array $orderBy = null)
 * @method Template|null findOneByCode(string $code)
 * @method Template      getOneByCode(string $code)
 * @method Template[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplateRepository extends ServiceEntityRepository
{
    use CodeTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Template::class);
    }

    public function findActiveByEventSiteId(string $event, int $siteId): array
    {
        return $this->createQueryBuilder('template')
            ->andWhere('template.event = :event')
            ->setParameter(':event', $event)
            ->andWhere('template.active = 1')
            ->leftJoin('template.sites', 'site')
            ->andWhere('site.id = :id')
            ->setParameter(':id', $siteId)
            ->getQuery()
            ->execute();
    }
}
