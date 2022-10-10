<?php

declare(strict_types=1);

namespace App\Content;

use App\Entity\Event;
use App\Repository\EventRepository;
use Sulu\Component\Content\Compat\PropertyInterface;
use Sulu\Component\Content\SimpleContentType;

class EventSelectionContentType extends SimpleContentType
{
    public function __construct(private readonly EventRepository $eventRepository)
    {
        parent::__construct('event_selection');
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
     * @return mixed[]
     */
    public function getViewData(PropertyInterface $property): array
    {
        return $property->getValue();
    }
}
