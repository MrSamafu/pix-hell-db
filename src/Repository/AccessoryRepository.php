<?php

namespace App\Repository;

use App\Entity\Accessory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Accessory>
 *
 * @method Accessory|null find($id, $lockMode = null, $lockVersion = null)
 * @method Accessory|null findOneBy(array $criteria, array $orderBy = null)
 * @method Accessory[]    findAll()
 * @method Accessory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Accessory::class);
    }

    /**
     * @return Accessory[] Returns an array of latest Accessories
     */
    public function findLatestAccessories(int $limit = 5): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findBySearch(string $search): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.name LIKE :search')
            ->orWhere('a.type LIKE :search')
            ->orWhere('a.compatibility LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.type = :type')
            ->setParameter('type', $type)
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCompatibility(string $console): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.compatibility LIKE :console')
            ->setParameter('console', '%'.$console.'%')
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
