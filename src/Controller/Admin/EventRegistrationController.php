<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\EventRegistration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventRegistrationController extends AbstractController
{
    public function __construct(private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory)
    {
    }

    #[Route(path: '/admin/api/events/{eventId}/registrations', methods: ['GET'], name: 'app.get_event_registration_list')]
    public function getListAction(int $eventId): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            EventRegistration::RESOURCE_KEY,
            ['eventId' => (string) $eventId],
        );

        return $this->json($listRepresentation->toArray());
    }
}
