<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Location;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Location>
 */
class LocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Location::class);
    }

    public function create(): Location
    {
        return new Location();
    }

    public function remove(int $id): void
    {
        /** @var object $location */
        $location = $this->getEntityManager()->getReference(
            $this->getClassName(),
            $id,
        );

        $this->getEntityManager()->remove($location);
        $this->getEntityManager()->flush();
    }

    public function save(Location $location): void
    {
        $this->getEntityManager()->persist($location);
        $this->getEntityManager()->flush();
    }

    public function findById(int $id): ?Location
    {
        return $this->find($id);
    }
}
