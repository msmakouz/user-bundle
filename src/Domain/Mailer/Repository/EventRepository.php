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
use Zentlix\MainBundle\Domain\Shared\Repository\GetTrait;
use Zentlix\UserBundle\Domain\Mailer\Entity\Event;

/**
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event      get($id, $lockMode = null, $lockVersion = null)
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    use GetTrait, CodeTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function assoc(): array
    {
        return array_column(
            $this->createQueryBuilder('a')
                ->select('a.id', 'a.title')
                ->orderBy('a.id', 'DESC')
                ->getQuery()
                ->execute(), 'id', 'title'
        );
    }
}