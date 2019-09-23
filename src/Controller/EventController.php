<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilderFactory;
use Sulu\Component\Rest\ListBuilder\Metadata\FieldDescriptorFactoryInterface;
use Sulu\Component\Rest\RestHelperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventController extends BaseRestController implements ClassResourceInterface
{
    /**
     * @var EventRepository
     */
    private $repository;

    public function __construct(
        EventRepository $repository,
        ViewHandlerInterface $viewHandler,
        RestHelperInterface $restHelper,
        DoctrineListBuilderFactory $listBuilderFactory,
        ?FieldDescriptorFactoryInterface $fieldDescriptorFactory
    ) {
        parent::__construct($viewHandler, $restHelper, $listBuilderFactory, $fieldDescriptorFactory);

        $this->repository = $repository;
    }

    /**
     * @Rest\Post("/events/{id}")
     */
    public function postTriggerAction(int $id, Request $request): Response
    {
        $event = $this->repository->findById($id, $request->query->get('locale'));
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

    protected function getResourceKey(): string
    {
        return Event::RESOURCE_KEY;
    }

    /**
     * @param Event $entity
     * @param string[] $data
     */
    protected function mapDataToEntity(array $data, object $entity): void
    {
        $entity->setTitle($data['title']);

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
    }

    protected function load(int $id, Request $request): ?object
    {
        return $this->repository->findById($id, $request->query->get('locale'));
    }

    protected function create(Request $request): object
    {
        return $this->repository->create($request->query->get('locale'));
    }

    /**
     * @param Event $entity
     */
    protected function save(object $entity): void
    {
        $this->repository->save($entity);
    }

    protected function remove(int $id): void
    {
        $this->repository->remove($id);
    }
}
