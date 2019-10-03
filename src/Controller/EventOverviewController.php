<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\LocationRepository;
use Sulu\Bundle\WebsiteBundle\Controller\WebsiteController;
use Sulu\Component\Content\Compat\StructureInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventOverviewController extends WebsiteController
{
    public function indexAction(
        Request $request,
        StructureInterface $structure,
        bool $preview = false,
        bool $partial = false
    ): Response {
        /** @var EventRepository $eventRepository */
        $eventRepository = $this->get(EventRepository::class);
        /** @var LocationRepository $locationRepository */
        $locationRepository = $this->get(LocationRepository::class);

        $locationId = $request->query->get('location');
        if ('' === $locationId) {
            $locationId = null;
        }

        $response = $this->renderStructure(
            $structure,
            [
                'events' => $eventRepository->filterByLocationId(
                    (int) $locationId,
                    $request->getLocale()
                ),
                'locations' => $locationRepository->findAll(),
            ],
            $preview,
            $partial
        );

        return $response;
    }

    /**
     * @return mixed[]
     */
    public static function getSubscribedServices(): array
    {
        return array_merge(
            parent::getSubscribedServices(),
            [
                EventRepository::class,
                LocationRepository::class,
            ]
        );
    }
}
