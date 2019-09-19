<?php

declare(strict_types=1);

namespace App\Tests\Functional\Traits;

use App\Entity\Event;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;

trait EventTrait
{
    public function createEvent(string $title, string $locale): Event
    {
        $event = $this->getEventRepository()->create($locale);
        $event->setTitle($title);

        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();

        return $event;
    }

    public function enableEvent(Event $event): void
    {
        $event->setEnabled(true);

        $this->getEntityManager()->flush();
    }

    public function findEventById(int $id, string $locale): ?Event
    {
        return $this->getEventRepository()->findById($id, $locale);
    }

    protected function getEventRepository(): EventRepository
    {
        return $this->getEntityManager()->getRepository(Event::class);
    }

    abstract protected function getEntityManager(): EntityManagerInterface;
}
