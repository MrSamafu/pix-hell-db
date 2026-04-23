<?php

namespace App\Repository;

use App\Entity\ConsoleKit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConsoleKit>
 */
class ConsoleKitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConsoleKit::class);
    }

    public function findByUserWithDetails(int $userId): array
    {
        return $this->createQueryBuilder('ck')
            ->andWhere('ck.user = :userId')
            ->setParameter('userId', $userId)
            ->leftJoin('ck.console', 'c')
            ->addSelect('c')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findTotalConsolesForUser(int $userId): int
    {
        return $this->createQueryBuilder('ck')
            ->select('SUM(ck.quantity)')
            ->andWhere('ck.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function findUsersWhoHave(int $consoleId): array
    {
        return $this->createQueryBuilder('ck')
            ->select('u.id', 'u.username', 'ck.quantity', 'ck.note')
            ->join('ck.user', 'u')
            ->where('ck.console = :consoleId')
            ->setParameter('consoleId', $consoleId)
            ->orderBy('u.username', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

