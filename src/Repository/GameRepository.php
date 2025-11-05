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

    public function findBySearch(string $search): array
    {
        return $this->createQueryBuilder('g')
            ->where('g.title LIKE :search')
            ->orWhere('g.platform LIKE :search')
            ->orWhere('g.genre LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('g.title', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
