<?php

namespace App\Repository;

use App\Entity\AccessoryCollection;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AccessoryCollection>
 *
 * @method AccessoryCollection|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessoryCollection|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessoryCollection[]    findAll()
 * @method AccessoryCollection[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessoryCollectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessoryCollection::class);
    }

    /**
     * @return AccessoryCollection[] Returns an array of user's accessory collection
     */
    public function findByUserWithDetails(int $userId): array
    {
        return $this->createQueryBuilder('ac')
            ->andWhere('ac.user = :userId')
            ->setParameter('userId', $userId)
            ->leftJoin('ac.accessory', 'a')
            ->addSelect('a')
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findTotalAccessoriesForUser(int $userId): int
    {
        return $this->createQueryBuilder('ac')
            ->select('SUM(ac.quantity)')
            ->andWhere('ac.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }

    public function findByUserAndType(int $userId, string $type): array
    {
        return $this->createQueryBuilder('ac')
            ->andWhere('ac.user = :userId')
            ->andWhere('a.type = :type')
            ->setParameter('userId', $userId)
            ->setParameter('type', $type)
            ->leftJoin('ac.accessory', 'a')
            ->addSelect('a')
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
