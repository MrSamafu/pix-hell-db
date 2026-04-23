<?php

namespace App\Repository;

use App\Entity\GameKit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameKit>
 */
class GameKitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameKit::class);
    }

    public function findByUserWithDetails(int $userId): array
    {
        return $this->createQueryBuilder('gk')
            ->andWhere('gk.user = :userId')
            ->setParameter('userId', $userId)
            ->leftJoin('gk.game', 'g')
            ->addSelect('g')
            ->orderBy('g.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findTotalGamesForUser(int $userId): int
    {
        return $this->createQueryBuilder('gk')
            ->select('SUM(gk.quantity)')
            ->andWhere('gk.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function findUsersWhoHave(int $gameId): array
    {
        return $this->createQueryBuilder('gk')
            ->select('u.id', 'u.username', 'gk.quantity', 'gk.note')
            ->join('gk.user', 'u')
            ->where('gk.game = :gameId')
            ->setParameter('gameId', $gameId)
            ->orderBy('u.username', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

