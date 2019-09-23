<?php

declare(strict_types=1);

namespace App\Admin;

use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilderFactory;
use Sulu\Component\Rest\ListBuilder\Doctrine\FieldDescriptor\DoctrineFieldDescriptor;
use Sulu\Component\Rest\ListBuilder\Metadata\FieldDescriptorFactoryInterface;
use Sulu\Component\Rest\ListBuilder\PaginatedRepresentation;
use Sulu\Component\Rest\RestHelperInterface;

class DoctrineListRepresentationFactory
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
        RestHelperInterface $restHelper,
        DoctrineListBuilderFactory $listBuilderFactory,
        FieldDescriptorFactoryInterface $fieldDescriptorFactory
    ) {
        $this->restHelper = $restHelper;
        $this->listBuilderFactory = $listBuilderFactory;
        $this->fieldDescriptorFactory = $fieldDescriptorFactory;
    }

    /**
     * @param mixed[] $filters
     */
    public function createDoctrineListRepresentation(
        string $resourceKey,
        array $filters = [],
        ?string $locale = null
    ): PaginatedRepresentation {
        /** @var DoctrineFieldDescriptor[] $fieldDescriptors */
        $fieldDescriptors = $this->fieldDescriptorFactory->getFieldDescriptors($resourceKey);

        $listBuilder = $this->listBuilderFactory->create($fieldDescriptors['id']->getEntityName());
        $this->restHelper->initializeListBuilder($listBuilder, $fieldDescriptors);
        if ($locale) {
            $listBuilder->setParameter('locale', $locale);
        }

        foreach ($filters as $key => $value) {
            $listBuilder->where($fieldDescriptors[$key], $value);
        }

        $list = $listBuilder->execute();

        return new PaginatedRepresentation(
            $list,
            $resourceKey,
            (int) $listBuilder->getCurrentPage(),
            (int) $listBuilder->getLimit(),
            (int) $listBuilder->count()
        );
    }
}
