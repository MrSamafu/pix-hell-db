<?php

namespace App\Repository;

use App\Entity\ConsoleCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConsoleCollection>
 *
 * @method ConsoleCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method ConsoleCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method ConsoleCollection[]    findAll()
 * @method ConsoleCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsoleCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConsoleCollection::class);
    }

    /**
     * @return ConsoleCollection[] Returns an array of user's console collection
     */
    public function findByUserWithDetails(int $userId): array
    {
        return $this->createQueryBuilder('cc')
            ->andWhere('cc.user = :userId')
            ->setParameter('userId', $userId)
            ->leftJoin('cc.console', 'c')
            ->addSelect('c')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findTotalConsolesForUser(int $userId): int
    {
        return $this->createQueryBuilder('cc')
            ->select('SUM(cc.quantity)')
            ->andWhere('cc.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    /**
     * Trouve tous les utilisateurs qui possèdent une console spécifique
     */
    public function findUsersWhoOwn(int $consoleId): array
    {
        return $this->createQueryBuilder('cc')
            ->select('u.id', 'u.username', 'cc.quantity')
            ->join('cc.user', 'u')
            ->where('cc.console = :consoleId')
            ->setParameter('consoleId', $consoleId)
            ->orderBy('u.username', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
