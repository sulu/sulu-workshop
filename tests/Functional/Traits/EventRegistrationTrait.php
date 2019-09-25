<?php

declare(strict_types=1);

namespace App\Tests\Functional\Traits;

use App\Entity\Event;
use App\Entity\EventRegistration;
use App\Repository\EventRegistrationRepository;
use Doctrine\ORM\EntityManagerInterface;

trait EventRegistrationTrait
{
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
