<?php

namespace App\Repository;

use App\Entity\GameCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameCollection>
 *
 * @method GameCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameCollection[]    findAll()
 * @method GameCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameCollection::class);
    }

    /**
     * @return GameCollection[] Returns an array of user's game collection
     */
    public function findByUserWithDetails(int $userId): array
    {
        return $this->createQueryBuilder('gc')
            ->andWhere('gc.user = :userId')
            ->setParameter('userId', $userId)
            ->leftJoin('gc.game', 'g')
            ->addSelect('g')
            ->orderBy('g.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findTotalGamesForUser(int $userId): int
    {
        return $this->createQueryBuilder('gc')
            ->select('SUM(gc.quantity)')
            ->andWhere('gc.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }
}
