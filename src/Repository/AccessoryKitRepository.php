<?php

namespace App\Repository;

use App\Entity\AccessoryKit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccessoryKit>
 */
class AccessoryKitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessoryKit::class);
    }

    public function findByUserWithDetails(int $userId): array
    {
        return $this->createQueryBuilder('ak')
            ->andWhere('ak.user = :userId')
            ->setParameter('userId', $userId)
            ->leftJoin('ak.accessory', 'a')
            ->addSelect('a')
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findTotalAccessoriesForUser(int $userId): int
    {
        return $this->createQueryBuilder('ak')
            ->select('SUM(ak.quantity)')
            ->andWhere('ak.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function findUsersWhoHave(int $accessoryId): array
    {
        return $this->createQueryBuilder('ak')
            ->select('u.id', 'u.username', 'ak.quantity', 'ak.note')
            ->join('ak.user', 'u')
            ->where('ak.accessory = :accessoryId')
            ->setParameter('accessoryId', $accessoryId)
            ->orderBy('u.username', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

