<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Repository\EventRepository;
use App\Repository\LocationRepository;
use Sulu\Bundle\WebsiteBundle\Controller\DefaultController;
use Sulu\Component\Content\Compat\StructureInterface;

class EventOverviewController extends DefaultController
{
    protected function getAttributes($attributes, StructureInterface $structure = null, $preview = false)
    {
        /** @var EventRepository $eventRepository */
        $eventRepository = $this->container->get(EventRepository::class);
        /** @var LocationRepository $locationRepository */
        $locationRepository = $this->container->get(LocationRepository::class);

        $request = $this->getRequest();
        $locationId = $request->query->get('location');

        $attributes = parent::getAttributes($attributes, $structure, $preview);
        $attributes['events'] = $eventRepository->filterByLocationId(
            $locationId ? (int) $locationId : null,
            $request->getLocale(),
        );
        $attributes['locations'] = $locationRepository->findAll();

        return $attributes;
    }

    public static function getSubscribedServices(): array
    {
        return \array_merge(
            parent::getSubscribedServices(),
            [
                EventRepository::class,
                LocationRepository::class,
            ],
        );
    }
}
