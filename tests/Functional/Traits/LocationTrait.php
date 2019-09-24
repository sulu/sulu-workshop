<?php

declare(strict_types=1);

namespace App\Tests\Functional\Traits;

use App\Entity\Location;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;

trait LocationTrait
{
    public function createLocation(string $name): Location
    {
        $location = $this->getLocationRepository()->create();
        $location->setName($name);
        $location->setStreet('');
        $location->setNumber('');
        $location->setPostalCode('');
        $location->setCity('');
        $location->setCountryCode('');

        $this->getEntityManager()->persist($location);
        $this->getEntityManager()->flush();

        return $location;
    }

    public function findLocationById(int $id): ?Location
    {
        return $this->getLocationRepository()->findById($id);
    }

    protected function getLocationRepository(): LocationRepository
    {
        return $this->getEntityManager()->getRepository(Location::class);
    }

    abstract protected function getEntityManager(): EntityManagerInterface;
}
