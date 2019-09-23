<?php

declare(strict_types=1);

namespace App\Controller;

use FOS\RestBundle\View\ViewHandlerInterface;
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilderFactory;
use Sulu\Component\Rest\ListBuilder\Doctrine\FieldDescriptor\DoctrineFieldDescriptor;
use Sulu\Component\Rest\ListBuilder\Metadata\FieldDescriptorFactoryInterface;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;
use Sulu\Component\Rest\RestController;
use Sulu\Component\Rest\RestHelperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

abstract class BaseRestController extends RestController
{
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
        ViewHandlerInterface $viewHandler,
        RestHelperInterface $restHelper,
        DoctrineListBuilderFactory $listBuilderFactory,
        ?FieldDescriptorFactoryInterface $fieldDescriptorFactory
    ) {
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
        $resourceKey = $this->getResourceKey();

        $locale = $request->query->get('locale');

        /** @var DoctrineFieldDescriptor[] $fieldDescriptors */
        $fieldDescriptors = $this->fieldDescriptorFactory->getFieldDescriptors($resourceKey);

        $listBuilder = $this->listBuilderFactory->create($fieldDescriptors['id']->getEntityName());
        $this->restHelper->initializeListBuilder($listBuilder, $fieldDescriptors);
        $listBuilder->setParameter('locale', $locale);

        $list = $listBuilder->execute();

        $listRepresentation = new PaginatedRepresentation(
            $list,
            $resourceKey,
            (int) $listBuilder->getCurrentPage(),
            (int) $listBuilder->getLimit(),
            (int) $listBuilder->count()
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

    abstract protected function getResourceKey(): string;

    protected function load(int $id, Request $request): ?object
    {
        throw new \RuntimeException('Not implemented');
    }

    protected function create(Request $request): object
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param string[] $data
     */
    protected function mapDataToEntity(array $data, object $entity): void
    {
        throw new \RuntimeException('Not implemented');
    }

    protected function save(object $entity): void
    {
        throw new \RuntimeException('Not implemented');
    }

    protected function remove(int $id): void
    {
        throw new \RuntimeException('Not implemented');
    }
}
