<?php

namespace App\Repository;

use App\Entity\Mode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Mode>
 *
 * @method Mode|null find($id, $lockMode = null, $lockVersion = null)
 * @method Mode|null findOneBy(array $criteria, array $orderBy = null)
 * @method Mode[]    findAll()
 * @method Mode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mode::class);
    }
}

