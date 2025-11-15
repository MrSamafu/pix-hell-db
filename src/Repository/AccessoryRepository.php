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

    /**
     * Recherche avancée d'accessoires avec filtres multiples
     *
     * @param array $criteria Critères de recherche :
     *   - search: recherche textuelle (nom, type, compatibilité)
     *   - type: type d'accessoire
     *   - compatibility: compatibilité
     *   - year: année de sortie
     *   - letter: première lettre du nom
     * @return Accessory[]
     */
    public function findBySearchAndFilters(array $criteria = []): array
    {
        $qb = $this->createQueryBuilder('a');

        // Recherche textuelle (nom, type, compatibilité)
        if (!empty($criteria['search'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('a.name', ':search'),
                    $qb->expr()->like('a.type', ':search'),
                    $qb->expr()->like('a.compatibility', ':search')
                )
            )
            ->setParameter('search', '%' . $criteria['search'] . '%');
        }

        // Filtre par type
        if (!empty($criteria['type'])) {
            $qb->andWhere('a.type = :type')
               ->setParameter('type', $criteria['type']);
        }

        // Filtre par compatibilité
        if (!empty($criteria['compatibility'])) {
            $qb->andWhere('a.compatibility LIKE :compatibility')
               ->setParameter('compatibility', '%' . $criteria['compatibility'] . '%');
        }

        // Filtre par année de sortie
        if (!empty($criteria['year'])) {
            $qb->andWhere('SUBSTRING(a.releaseDate, 1, 4) = :year')
               ->setParameter('year', $criteria['year']);
        }

        // Filtre par première lettre (recherche alphabétique)
        if (!empty($criteria['letter'])) {
            if ($criteria['letter'] === '0-9') {
                $qb->andWhere('a.name REGEXP :regex')
                   ->setParameter('regex', '^[0-9]');
            } else {
                $qb->andWhere('a.name LIKE :letter')
                   ->setParameter('letter', $criteria['letter'] . '%');
            }
        }

        return $qb->orderBy('a.name', 'ASC')
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
        $result = $this->createQueryBuilder('a')
            ->select('DISTINCT SUBSTRING(a.releaseDate, 1, 4) as year')
            ->where('a.releaseDate IS NOT NULL')
            ->orderBy('year', 'DESC')
            ->getQuery()
            ->getScalarResult();

        return array_map(fn($item) => $item['year'], $result);
    }

    /**
     * Récupère tous les types disponibles
     *
     * @return array
     */
    public function findAvailableTypes(): array
    {
        $result = $this->createQueryBuilder('a')
            ->select('DISTINCT a.type')
            ->orderBy('a.type', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_map(fn($item) => $item['type'], $result);
    }

    /**
     * Récupère toutes les compatibilités disponibles
     *
     * @return array
     */
    public function findAvailableCompatibilities(): array
    {
        $result = $this->createQueryBuilder('a')
            ->select('DISTINCT a.compatibility')
            ->orderBy('a.compatibility', 'ASC')
            ->getQuery()
            ->getScalarResult();

        return array_map(fn($item) => $item['compatibility'], $result);
    }

    /**
     * Recherche simple d'accessoires par nom
     */
    public function searchByName(string $query): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.name LIKE :query')
            ->orWhere('a.type LIKE :query')
            ->orWhere('a.compatibility LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('a.name', 'ASC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();
    }
}
