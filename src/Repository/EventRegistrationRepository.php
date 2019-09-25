<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event;
use App\Entity\EventRegistration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method EventRegistration|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventRegistration|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventRegistration[]    findAll()
 * @method EventRegistration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
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
        $registration = new EventRegistration($event);

        return $registration;
    }

    public function save(EventRegistration $registration): void
    {
        $this->getEntityManager()->persist($registration);
        $this->getEntityManager()->flush();
    }
}
