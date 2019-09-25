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
        return static::getEntityManager()->getRepository(EventRegistration::class);
    }

    abstract protected static function getEntityManager(): EntityManagerInterface;
}
