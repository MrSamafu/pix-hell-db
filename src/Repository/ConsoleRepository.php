<?php

namespace App\Repository;

use App\Entity\Console;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Console>
 *
 * @method Console|null find($id, $lockMode = null, $lockVersion = null)
 * @method Console|null findOneBy(array $criteria, array $orderBy = null)
 * @method Console[]    findAll()
 * @method Console[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConsoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Console::class);
    }

    /**
     * @return Console[] Returns an array of latest Consoles
     */
    public function findLatestConsoles(int $limit = 5): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findBySearch(string $search): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.name LIKE :search')
            ->orWhere('c.manufacturer LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByGeneration(int $generation): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.generation = :generation')
            ->setParameter('generation', $generation)
            ->orderBy('c.releaseDate', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
