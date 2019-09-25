<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event;
use App\Entity\EventRegistration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventRegistration>
 */
class EventRegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventRegistration::class);
    }

    public function create(Event $event): EventRegistration
    {
        return new EventRegistration($event);
    }

    public function save(EventRegistration $registration): void
    {
        $this->getEntityManager()->persist($registration);
        $this->getEntityManager()->flush();
    }
}
