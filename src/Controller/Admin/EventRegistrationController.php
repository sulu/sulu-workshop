<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Admin\DoctrineListRepresentationFactory;
use App\Entity\EventRegistration;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Sulu\Component\Rest\RestController;
use Symfony\Component\HttpFoundation\Response;

class EventRegistrationController extends RestController implements ClassResourceInterface
{
    /**
     * @var DoctrineListRepresentationFactory
     */
    private $doctrineListRepresentationFactory;

    public function __construct(DoctrineListRepresentationFactory $doctrineListRepresentationFactory)
    {
        $this->doctrineListRepresentationFactory = $doctrineListRepresentationFactory;
    }

    public function cgetAction(int $eventId): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            EventRegistration::RESOURCE_KEY,
            ['eventId' => $eventId]
        );

        return $this->handleView($this->view($listRepresentation));
    }
}
