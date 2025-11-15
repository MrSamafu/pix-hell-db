<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 *
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /**
     * @return Game[] Returns an array of latest Games
     */
    public function findLatestGames(int $limit = 5): array
    {
        return $this->createQueryBuilder('g')
            ->orderBy('g.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche avancée de jeux avec filtres multiples
     *
     * @param array $criteria Critères de recherche :
     *   - search: recherche textuelle (titre, série, éditeur, développeur)
     *   - platform: ID de la console
     *   - year: année de sortie
     *   - genre: ID du genre
     *   - mode: ID du mode
     *   - letter: première lettre du titre
     * @return Game[]
     */
    public function findBySearchAndFilters(array $criteria = []): array
    {
        $qb = $this->createQueryBuilder('g')
            ->leftJoin('g.platform', 'p')
            ->leftJoin('g.genres', 'ge')
            ->leftJoin('g.modes', 'm')
            ->addSelect('p', 'ge', 'm');

        // Recherche textuelle (titre, série, éditeur, développeur)
        if (!empty($criteria['search'])) {
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like('g.title', ':search'),
                    $qb->expr()->like('g.series', ':search'),
                    $qb->expr()->like('g.publisher', ':search'),
                    $qb->expr()->like('g.developer', ':search')
                )
            )
            ->setParameter('search', '%' . $criteria['search'] . '%');
        }

        // Filtre par plateforme (console)
        if (!empty($criteria['platform'])) {
            $qb->andWhere('p.id = :platform')
               ->setParameter('platform', $criteria['platform']);
        }

        // Filtre par année de sortie
        if (!empty($criteria['year'])) {
            $qb->andWhere('SUBSTRING(g.releaseDate, 1, 4) = :year')
               ->setParameter('year', $criteria['year']);
        }

        // Filtre par genre
        if (!empty($criteria['genre'])) {
            $qb->andWhere('ge.id = :genre')
               ->setParameter('genre', $criteria['genre']);
        }

        // Filtre par mode
        if (!empty($criteria['mode'])) {
            $qb->andWhere('m.id = :mode')
               ->setParameter('mode', $criteria['mode']);
        }

        // Filtre par première lettre (recherche alphabétique)
        if (!empty($criteria['letter'])) {
            if ($criteria['letter'] === '0-9') {
                // Commence par un chiffre
                $qb->andWhere('g.title REGEXP :regex')
                   ->setParameter('regex', '^[0-9]');
            } else {
                // Commence par une lettre spécifique
                $qb->andWhere('g.title LIKE :letter')
                   ->setParameter('letter', $criteria['letter'] . '%');
            }
        }

        return $qb->orderBy('g.title', 'ASC')
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
        $result = $this->createQueryBuilder('g')
            ->select('DISTINCT SUBSTRING(g.releaseDate, 1, 4) as year')
            ->where('g.releaseDate IS NOT NULL')
            ->orderBy('year', 'DESC')
            ->getQuery()
            ->getScalarResult();

        return array_map(fn($item) => $item['year'], $result);
    }

    /**
     * Recherche simple de jeux par titre
     */
    public function searchByTitle(string $query): array
    {
        return $this->createQueryBuilder('g')
            ->where('g.title LIKE :query')
            ->orWhere('g.series LIKE :query')
            ->orWhere('g.publisher LIKE :query')
            ->orWhere('g.developer LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('g.title', 'ASC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();
    }
}
