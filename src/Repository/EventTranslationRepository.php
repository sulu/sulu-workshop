<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EventTranslation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EventTranslation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventTranslation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventTranslation[]    findAll()
 * @method EventTranslation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventTranslationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventTranslation::class);
    }

    // /**
    //  * @return EventTranslation[] Returns an array of EventTranslation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EventTranslation
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
