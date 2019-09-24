<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Entity\Location;
use App\Repository\LocationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @phpstan-type LocationData array{
 *     id: int|null,
 *     name: string,
 *     street: string|null,
 *     number: string|null,
 *     postalCode: string|null,
 *     city: string|null,
 *     countryCode: string|null,
 * }
 */
class LocationController extends AbstractController
{
    public function __construct(
        private readonly LocationRepository $locationRepository,
        private readonly DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
    ) {
    }

    #[Route(path: '/admin/api/locations/{id}', methods: ['GET'], name: 'app.get_location')]
    public function getAction(int $id): Response
    {
        $location = $this->load($id);
        if (!$location instanceof Location) {
            throw new NotFoundHttpException();
        }

        return $this->json($this->getDataForEntity($location));
    }

    #[Route(path: '/admin/api/locations/{id}', methods: ['PUT'], name: 'app.put_location')]
    public function putAction(int $id, Request $request): Response
    {
        $location = $this->load($id);
        if (!$location instanceof Location) {
            throw new NotFoundHttpException();
        }

        /** @var LocationData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $location);
        $this->save($location);

        return $this->json($this->getDataForEntity($location));
    }

    #[Route(path: '/admin/api/locations', methods: ['POST'], name: 'app.post_location')]
    public function postAction(Request $request): Response
    {
        $location = $this->create();

        /** @var LocationData $data */
        $data = $request->toArray();
        $this->mapDataToEntity($data, $location);
        $this->save($location);

        return $this->json($this->getDataForEntity($location), 201);
    }

    #[Route(path: '/admin/api/locations/{id}', methods: ['DELETE'], name: 'app.delete_location')]
    public function deleteAction(int $id): Response
    {
        $this->remove($id);

        return $this->json(null, 204);
    }

    #[Route(path: '/admin/api/locations', methods: ['GET'], name: 'app.get_location_list')]
    public function getListAction(): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Location::RESOURCE_KEY,
        );

        return $this->json($listRepresentation->toArray());
    }

    /**
     * @return LocationData
     */
    protected function getDataForEntity(Location $entity): array
    {
        return [
            'id' => $entity->getId(),
            'name' => $entity->getName() ?? '',
            'street' => $entity->getStreet(),
            'number' => $entity->getNumber(),
            'postalCode' => $entity->getPostalCode(),
            'city' => $entity->getCity(),
            'countryCode' => $entity->getCountryCode(),
        ];
    }

    /**
     * @param LocationData $data
     */
    protected function mapDataToEntity(array $data, Location $entity): void
    {
        $entity->setName($data['name']);
        $entity->setStreet($data['street'] ?? '');
        $entity->setNumber($data['number'] ?? '');
        $entity->setPostalCode($data['postalCode'] ?? '');
        $entity->setCity($data['city'] ?? '');
        $entity->setCountryCode($data['countryCode'] ?? '');
    }

    protected function load(int $id): ?Location
    {
        return $this->locationRepository->findById($id);
    }

    protected function create(): Location
    {
        return $this->locationRepository->create();
    }

    protected function save(Location $entity): void
    {
        $this->locationRepository->save($entity);
    }

    protected function remove(int $id): void
    {
        $this->locationRepository->remove($id);
    }
}
