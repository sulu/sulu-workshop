<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\Event;
use App\Repository\EventRepository;
use Sulu\Bundle\MediaBundle\Entity\MediaRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class EventController extends AbstractController
{
    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var MediaRepositoryInterface
     */
    private $mediaRepository;

    /**
     * @var DoctrineListRepresentationFactory
     */
    private $doctrineListRepresentationFactory;

    public function __construct(
        EventRepository $repository,
        MediaRepositoryInterface $mediaRepository,
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
    ) {
        $this->eventRepository = $repository;
        $this->mediaRepository = $mediaRepository;
        $this->doctrineListRepresentationFactory = $doctrineListRepresentationFactory;
    }

    /**
     * @Route("/admin/api/events/{id}", methods={"GET"}, name="app.get_event")
     */
    public function getAction(int $id, Request $request): Response
    {
        $event = $this->load($id, $request);
        if (!$event) {
            throw new NotFoundHttpException();
        }

        return $this->json($this->getDataForEntity($event));
    }

    /**
     * @Route("/admin/api/events/{id}", methods={"PUT"}, name="app.put_event")
     */
    public function putAction(int $id, Request $request): Response
    {
        $event = $this->load($id, $request);
        if (!$event) {
            throw new NotFoundHttpException();
        }

        $this->mapDataToEntity($request->toArray(), $event);
        $this->save($event);

        return $this->json($this->getDataForEntity($event));
    }

    /**
     * @Route("/admin/api/events", methods={"POST"}, name="app.post_event")
     */
    public function postAction(Request $request): Response
    {
        $event = $this->create($request);

        $this->mapDataToEntity($request->toArray(), $event);
        $this->save($event);

        return $this->json($this->getDataForEntity($event), 201);
    }

    /**
     * @Route("/admin/api/events/{id}", methods={"POST"}, name="app.post_event_trigger")
     */
    public function postTriggerAction(int $id, Request $request): Response
    {
        $event = $this->eventRepository->findById($id, (string) $this->getLocale($request));
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

        $this->eventRepository->save($event);

        return $this->json($this->getDataForEntity($event));
    }

    /**
     * @Route("/admin/api/events/{id}", methods={"DELETE"}, name="app.delete_event")
     */
    public function deleteAction(int $id): Response
    {
        $this->remove($id);

        return $this->json(null, 204);
    }

    /**
     * @Route("/admin/api/events", methods={"GET"}, name="app.get_event_list")
     */
    public function getListAction(Request $request): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Event::RESOURCE_KEY,
            [],
            ['locale' => $this->getLocale($request)]
        );

        return $this->json($listRepresentation->toArray());
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDataForEntity(Event $entity): array
    {
        $image = $entity->getImage();
        $startDate = $entity->getStartDate();
        $endDate = $entity->getEndDate();

        return [
            'id' => $entity->getId(),
            'enabled' => $entity->isEnabled(),
            'title' => $entity->getTitle(),
            'image' => $image
                ? ['id' => $image->getId()]
                : null,
            'teaser' => $entity->getTeaser(),
            'description' => $entity->getDescription(),
            'startDate' => $startDate ? $startDate->format('c') : null,
            'endDate' => $endDate ? $endDate->format('c') : null,
            'location' => $entity->getLocation(),
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function mapDataToEntity(array $data, Event $entity): void
    {
        $imageId = $data['image']['id'] ?? null;

        $entity->setTitle($data['title']);
        $entity->setImage($imageId ? $this->mediaRepository->findMediaById($imageId) : null);
        $entity->setTeaser($data['teaser'] ?? '');
        $entity->setDescription($data['description'] ?? '');
        $entity->setStartDate($data['startDate'] ? new \DateTimeImmutable($data['startDate']) : null);
        $entity->setEndDate($data['endDate'] ? new \DateTimeImmutable($data['endDate']) : null);
        $entity->setLocation($data['location'] ?? null);
    }

    protected function load(int $id, Request $request): ?Event
    {
        return $this->eventRepository->findById($id, (string) $this->getLocale($request));
    }

    protected function create(Request $request): Event
    {
        return $this->eventRepository->create((string) $this->getLocale($request));
    }

    protected function save(Event $entity): void
    {
        $this->eventRepository->save($entity);
    }

    protected function remove(int $id): void
    {
        $this->eventRepository->remove($id);
    }

    public function getLocale(Request $request): ?string
    {
        return $request->query->has('locale') ? (string) $request->query->get('locale') : null;
    }
}
