<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Group\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Zentlix\MainBundle\Domain\Shared\Repository\CodeTrait;
use Zentlix\MainBundle\Domain\Shared\Repository\MaxSortTrait;
use Zentlix\UserBundle\Domain\Group\Entity\UserGroup;

/**
 * @method UserGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    use CodeTrait, MaxSortTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserGroup::class);
    }

    public function findAll()
    {
        return $this->findBy([], ['sort' => 'ASC']);
    }

    public function assoc(): array
    {
        return array_column(
            $this->createQueryBuilder('a')
                ->select('a.code', 'a.title')
                ->orderBy('a.sort')
                ->getQuery()
                ->execute(), 'code', 'title'
        );
    }
}
