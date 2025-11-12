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

    /**
     * Recherche avancée de consoles avec filtres multiples
     *
     * @param array $criteria Critères de recherche :
     *   - search: recherche textuelle (nom, fabricant)
     *   - manufacturer: fabricant
     *   - generation: génération
     *   - year: année de sortie
     *   - letter: première lettre du nom
     * @return Console[]
     */
    public function findBySearchAndFilters(array $criteria = []): array
    {
        $qb = $this->createQueryBuilder('c');

        // Recherche textuelle (nom, fabricant)
        if (!empty($criteria['search'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('c.name', ':search'),
                    $qb->expr()->like('c.manufacturer', ':search')
                )
            )
            ->setParameter('search', '%' . $criteria['search'] . '%');
        }

        // Filtre par fabricant
        if (!empty($criteria['manufacturer'])) {
            $qb->andWhere('c.manufacturer = :manufacturer')
               ->setParameter('manufacturer', $criteria['manufacturer']);
        }

        // Filtre par génération
        if (!empty($criteria['generation'])) {
            $qb->andWhere('c.generation = :generation')
               ->setParameter('generation', $criteria['generation']);
        }

        // Filtre par année de sortie
        if (!empty($criteria['year'])) {
            $qb->andWhere('SUBSTRING(c.releaseDate, 1, 4) = :year')
               ->setParameter('year', $criteria['year']);
        }

        // Filtre par première lettre (recherche alphabétique)
        if (!empty($criteria['letter'])) {
            if ($criteria['letter'] === '0-9') {
                $qb->andWhere('c.name REGEXP :regex')
                   ->setParameter('regex', '^[0-9]');
            } else {
                $qb->andWhere('c.name LIKE :letter')
                   ->setParameter('letter', $criteria['letter'] . '%');
            }
        }

        return $qb->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère toutes les années de sortie disponibles
     *
     * @return array
     */
    public function findAvailableYears(): array
    {
        $result = $this->createQueryBuilder('c')
            ->select('DISTINCT SUBSTRING(c.releaseDate, 1, 4) as year')
            ->where('c.releaseDate IS NOT NULL')
            ->orderBy('year', 'DESC')
            ->getQuery()
            ->getScalarResult();

        return array_map(fn($item) => $item['year'], $result);
    }

    /**
     * Récupère tous les fabricants disponibles
     *
     * @return array
     */
    public function findAvailableManufacturers(): array
    {
        $result = $this->createQueryBuilder('c')
            ->select('DISTINCT c.manufacturer')
            ->orderBy('c.manufacturer', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_map(fn($item) => $item['manufacturer'], $result);
    }

    /**
     * Récupère toutes les générations disponibles
     *
     * @return array
     */
    public function findAvailableGenerations(): array
    {
        $result = $this->createQueryBuilder('c')
            ->select('DISTINCT c.generation')
            ->orderBy('c.generation', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_map(fn($item) => $item['generation'], $result);
    }
}