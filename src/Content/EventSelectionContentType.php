<?php

declare(strict_types=1);

namespace App\Content;

use App\Entity\Event;
use App\Repository\EventRepository;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Component\Content\SimpleContentType;

class EventSelectionContentType extends SimpleContentType
{
    /**
     * @var EventRepository
     */
    private $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        parent::__construct('event_selection');

        $this->eventRepository = $eventRepository;
    }

    /**
     * @return Event[]
     */
    public function getContentData(PropertyInterface $property): array
    {
        $ids = $property->getValue();
        $locale = $property->getStructure()->getLanguageCode();

        $events = [];
        foreach ($ids ?: [] as $id) {
            $event = $this->eventRepository->findById((int) $id, $locale);
            if ($event && $event->isEnabled()) {
                $events[] = $event;
            }
        }

        return $events;
    }

    /**
     * {@inheritdoc}
     */
    public function getViewData(PropertyInterface $property)
    {
        return $property->getValue();
    }
}
