<?php

declare(strict_types=1);

namespace App\Tests\Functional\Traits;

use App\Entity\Event;
use App\Entity\EventRegistration;
use App\Repository\EventRegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;

trait EventRegistrationTrait
{
    public function createEventRegistration(Event $event, string $firstName, string $lastName): EventRegistration
    {
        $event = $this->getEventRegistrationRepository()->create($event);
        $event->setFirstName($firstName);
        $event->setLastName($lastName);
        $event->setEmail($firstName . '@' . $lastName . '.at');
        $event->setMessage('');

        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();

        return $event;
    }

    /**
     * @return EventRegistration[]
     */
    public function findEventRegistrations(Event $event): array
    {
        return $this->getEventRegistrationRepository()->findBy(['event' => $event]);
    }

    protected function getEventRegistrationRepository(): EventRegistrationRepository
    {
        return $this->getEntityManager()->getRepository(EventRegistration::class);
    }

    abstract protected function getEntityManager(): EntityManagerInterface;
}
