<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\LocationRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaRepositoryInterface;
use Sulu\Component\Rest\AbstractRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @RouteResource("event")
 */
class EventController extends AbstractRestController implements ClassResourceInterface
{
    /**
     * @var DoctrineListRepresentationFactory
     */
    private $doctrineListRepresentationFactory;

    /**
     * @var EventRepository
     */
    private $repository;

    /**
     * @var MediaRepositoryInterface
     */
    private $mediaRepository;

    /**
     * @var LocationRepository
     */
    private $locationRepository;

    public function __construct(
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        EventRepository $repository,
        MediaRepositoryInterface $mediaRepository,
        LocationRepository $locationRepository,
        ViewHandlerInterface $viewHandler,
        ?TokenStorageInterface $tokenStorage = null
    ) {
        $this->doctrineListRepresentationFactory = $doctrineListRepresentationFactory;
        $this->repository = $repository;
        $this->mediaRepository = $mediaRepository;
        $this->locationRepository = $locationRepository;

        parent::__construct($viewHandler, $tokenStorage);
    }

    public function cgetAction(Request $request): Response
    {
        $locale = $request->query->get('locale');
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Event::RESOURCE_KEY,
            [],
            ['locale' => $locale]
        );

        return $this->handleView($this->view($listRepresentation));
    }

    public function getAction(int $id, Request $request): Response
    {
        $entity = $this->load($id, $request);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

        return $this->handleView($this->view($entity));
    }

    public function postAction(Request $request): Response
    {
        $entity = $this->create($request);

        $this->mapDataToEntity($request->request->all(), $entity);

        $this->save($entity);

        return $this->handleView($this->view($entity));
    }

    /**
     * @Rest\Post("/events/{id}")
     */
    public function postTriggerAction(int $id, Request $request): Response
    {
        $event = $this->repository->findById($id, (string) $this->getLocale($request));
        if (!$event) {
            throw new NotFoundHttpException();
        }

        switch ($request->query->get('action')) {
            case 'enable':
                $event->setEnabled(true);
                break;
            case 'disable':
                $event->setEnabled(false);
                break;
        }

        $this->repository->save($event);

        return $this->handleView($this->view($event));
    }

    public function putAction(int $id, Request $request): Response
    {
        $entity = $this->load($id, $request);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $this->mapDataToEntity($request->request->all(), $entity);

        $this->save($entity);

        return $this->handleView($this->view($entity));
    }

    public function deleteAction(int $id): Response
    {
        $this->remove($id);

        return $this->handleView($this->view());
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function mapDataToEntity(array $data, Event $entity): void
    {
        $entity->setTitle($data['title']);

        $image = null;
        if ($imageId = ($data['image']['id'] ?? null)) {
            $image = $this->mediaRepository->findMediaById($imageId);
        }
        $entity->setImage($image);

        if ($teaser = $data['teaser'] ?? null) {
            $entity->setTeaser($teaser);
        }

        if ($description = $data['description'] ?? null) {
            $entity->setDescription($description);
        }

        if ($startDate = $data['startDate'] ?? null) {
            $entity->setStartDate(new \DateTimeImmutable($startDate));
        }

        if ($endDate = $data['endDate'] ?? null) {
            $entity->setEndDate(new \DateTimeImmutable($endDate));
        }

        if ($locationId = $data['locationId'] ?? null) {
            $entity->setLocation(
                $this->locationRepository->findById((int) $locationId)
            );
        }
    }

    protected function load(int $id, Request $request): ?Event
    {
        return $this->repository->findById($id, (string) $this->getLocale($request));
    }

    protected function create(Request $request): Event
    {
        return $this->repository->create((string) $this->getLocale($request));
    }

    protected function save(Event $entity): void
    {
        $this->repository->save($entity);
    }

    protected function remove(int $id): void
    {
        $this->repository->remove($id);
    }
}
