<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Event;
use App\Repository\EventRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilderFactory;
use Sulu\Component\Rest\ListBuilder\FieldDescriptorInterface;
use Sulu\Component\Rest\ListBuilder\ListRepresentation;
use Sulu\Component\Rest\ListBuilder\Metadata\FieldDescriptorFactoryInterface;
use Sulu\Component\Rest\RestController;
use Sulu\Component\Rest\RestHelperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventController extends RestController implements ClassResourceInterface
{
    /**
     * @var EventRepository
     */
    private $repository;

    /**
     * @var RestHelperInterface
     */
    private $restHelper;

    /**
     * @var DoctrineListBuilderFactory
     */
    private $listBuilderFactory;

    /**
     * @var FieldDescriptorFactoryInterface
     */
    private $fieldDescriptorFactory;

    public function __construct(
        EventRepository $repository,
        ViewHandlerInterface $viewHandler,
        RestHelperInterface $restHelper,
        DoctrineListBuilderFactory $listBuilderFactory,
        ?FieldDescriptorFactoryInterface $fieldDescriptorFactory
    ) {
        $this->repository = $repository;
        $this->restHelper = $restHelper;
        $this->listBuilderFactory = $listBuilderFactory;

        if (!$fieldDescriptorFactory) {
            throw new \RuntimeException(
                'FieldDescriptorFactory cannot be null - is it possible that you call this in the website context.'
            );
        }
        $this->fieldDescriptorFactory = $fieldDescriptorFactory;

        $this->setViewHandler($viewHandler);
    }

    public function cgetAction(Request $request): Response
    {
        $locale = $request->query->get('locale');

        /** @var FieldDescriptorInterface[] $fieldDescriptors */
        $fieldDescriptors = $this->fieldDescriptorFactory->getFieldDescriptors('events');

        $listBuilder = $this->listBuilderFactory->create(Event::class);
        $this->restHelper->initializeListBuilder($listBuilder, $fieldDescriptors);
        $listBuilder->setParameter('locale', $locale);

        $listResponse = $listBuilder->execute();

        $listRepresentation = new ListRepresentation(
            $listResponse,
            'events',
            'app.get_events',
            $request->query->all(),
            $listBuilder->getCurrentPage(),
            $listBuilder->getLimit(),
            $listBuilder->count()
        );

        return $this->handleView($this->view($listRepresentation));
    }

    public function getAction(int $id, Request $request): Response
    {
        $event = $this->repository->findById($id, $request->query->get('locale'));
        if (!$event) {
            throw new NotFoundHttpException();
        }

        return $this->handleView($this->view($event));
    }

    public function postAction(Request $request): Response
    {
        $event = $this->repository->create($request->query->get('locale'));

        $this->mapDataToEvent($request->request->all(), $event);

        $this->repository->save();

        return $this->handleView($this->view($event));
    }

    public function putAction(int $id, Request $request): Response
    {
        $event = $this->repository->findById($id, $request->query->get('locale'));
        if (!$event) {
            throw new NotFoundHttpException();
        }

        $this->mapDataToEvent($request->request->all(), $event);

        $this->repository->save();

        return $this->handleView($this->view($event));
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

        $this->repository->save();

        return $this->handleView($this->view($event));
    }

    public function deleteAction(int $id, Request $request): Response
    {
        $event = $this->repository->findById($id, $request->query->get('locale'));
        if (!$event) {
            throw new NotFoundHttpException();
        }

        $this->repository->remove($event);

        return $this->handleView($this->view());
    }

    /**
     * @param string[] $data
     */
    protected function mapDataToEvent(array $data, Event $event): void
    {
        $event->setTitle($data['title']);

        if ($description = $data['description'] ?? null) {
            $event->setDescription($description);
        }

        if ($startDate = $data['startDate'] ?? null) {
            $event->setStartDate(new \DateTimeImmutable($startDate));
        }

        if ($endDate = $data['endDate'] ?? null) {
            $event->setEndDate(new \DateTimeImmutable($endDate));
        }
    }
}
