<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Admin;

use App\Controller\Admin\LocationController;
use App\Tests\Functional\Traits\LocationTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

/**
 * @phpstan-import-type LocationData from LocationController
 */
class LocationControllerTest extends SuluTestCase
{
    use LocationTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = $this->createAuthenticatedClient();
        $this->purgeDatabase();
    }

    public function testGetList(): void
    {
        $location1 = $this->createLocation('Sulu');
        $location2 = $this->createLocation('Symfony');

        $this->client->jsonRequest('GET', '/admin/api/locations');

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        /**
         * @var array{
         *     _embedded: array{
         *         locations: array<array{
         *             id: int,
         *             name: string,
         *         }>
         *     },
         *     total: int,
         * } $result
         */
        $result = \json_decode($response->getContent() ?: '', true, 512, \JSON_THROW_ON_ERROR);
        $this->assertHttpStatusCode(200, $response);

        $this->assertSame(2, $result['total']);
        $this->assertCount(2, $result['_embedded']['locations']);
        $items = $result['_embedded']['locations'];

        $this->assertSame($location1->getId(), $items[0]['id']);
        $this->assertSame($location2->getId(), $items[1]['id']);

        $this->assertSame($location1->getName(), $items[0]['name']);
        $this->assertSame($location2->getName(), $items[1]['name']);
    }

    public function testGet(): void
    {
        $location = $this->createLocation('Sulu');

        $this->client->jsonRequest('GET', '/admin/api/locations/' . $location->getId());

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        /** @var LocationData $result */
        $result = \json_decode($response->getContent() ?: '', true, 512, \JSON_THROW_ON_ERROR);
        $this->assertHttpStatusCode(200, $response);

        $this->assertSame($location->getId(), $result['id']);
        $this->assertSame($location->getName(), $result['name']);
    }

    public function testPost(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/admin/api/locations',
            [
                'name' => 'Sulu',
                'street' => 'Teststreet',
                'number' => '42',
                'postalCode' => '6850',
                'city' => 'Dornbirn',
                'countryCode' => 'AT',
            ],
        );

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        /** @var LocationData $result */
        $result = \json_decode($response->getContent() ?: '', true, 512, \JSON_THROW_ON_ERROR);
        $this->assertHttpStatusCode(201, $response);

        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
        $this->assertSame('Sulu', $result['name']);
        $this->assertSame('Teststreet', $result['street']);
        $this->assertSame('42', $result['number']);
        $this->assertSame('6850', $result['postalCode']);
        $this->assertSame('Dornbirn', $result['city']);
        $this->assertSame('AT', $result['countryCode']);

        $result = $this->findLocationById($result['id']);

        $this->assertNotNull($result);
        $this->assertSame('Sulu', $result->getName());
        $this->assertSame('Teststreet', $result->getStreet());
        $this->assertSame('42', $result->getNumber());
        $this->assertSame('6850', $result->getPostalCode());
        $this->assertSame('Dornbirn', $result->getCity());
        $this->assertSame('AT', $result->getCountryCode());
    }

    public function testPostNullValues(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/admin/api/locations',
            [
                'name' => 'Sulu',
            ],
        );

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        /** @var LocationData $result */
        $result = \json_decode($response->getContent() ?: '', true, 512, \JSON_THROW_ON_ERROR);
        $this->assertHttpStatusCode(201, $response);

        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
        $this->assertSame('Sulu', $result['name']);
        $this->assertEmpty($result['street']);
        $this->assertEmpty($result['number']);
        $this->assertEmpty($result['postalCode']);
        $this->assertEmpty($result['city']);
        $this->assertEmpty($result['countryCode']);

        $result = $this->findLocationById($result['id']);

        $this->assertNotNull($result);
        $this->assertSame('Sulu', $result->getName());
        $this->assertEmpty($result->getStreet());
        $this->assertEmpty($result->getNumber());
        $this->assertEmpty($result->getPostalCode());
        $this->assertEmpty($result->getCity());
        $this->assertEmpty($result->getCountryCode());
    }

    public function testPut(): void
    {
        $location = $this->createLocation('Symfony');

        $this->client->jsonRequest(
            'PUT',
            '/admin/api/locations/' . $location->getId(),
            [
                'name' => 'Sulu',
                'street' => 'Teststreet',
                'number' => '42',
                'postalCode' => '6850',
                'city' => 'Dornbirn',
                'countryCode' => 'AT',
            ],
        );

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        /** @var LocationData $result */
        $result = \json_decode($response->getContent() ?: '', true, 512, \JSON_THROW_ON_ERROR);
        $this->assertHttpStatusCode(200, $response);

        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
        $this->assertSame('Sulu', $result['name']);
        $this->assertSame('Teststreet', $result['street']);
        $this->assertSame('42', $result['number']);
        $this->assertSame('6850', $result['postalCode']);
        $this->assertSame('Dornbirn', $result['city']);
        $this->assertSame('AT', $result['countryCode']);

        $result = $this->findLocationById($result['id']);

        $this->assertNotNull($result);
        $this->assertSame('Sulu', $result->getName());
        $this->assertSame('Teststreet', $result->getStreet());
        $this->assertSame('42', $result->getNumber());
        $this->assertSame('6850', $result->getPostalCode());
        $this->assertSame('Dornbirn', $result->getCity());
        $this->assertSame('AT', $result->getCountryCode());
    }

    public function testPutNullValues(): void
    {
        $location = $this->createLocation('Symfony');

        $this->client->jsonRequest(
            'PUT',
            '/admin/api/locations/' . $location->getId(),
            [
                'name' => 'Sulu',
            ],
        );

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        /** @var LocationData $result */
        $result = \json_decode($response->getContent() ?: '', true, 512, \JSON_THROW_ON_ERROR);
        $this->assertHttpStatusCode(200, $response);

        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
        $this->assertSame('Sulu', $result['name']);
        $this->assertEmpty($result['street']);
        $this->assertEmpty($result['number']);
        $this->assertEmpty($result['postalCode']);
        $this->assertEmpty($result['city']);
        $this->assertEmpty($result['countryCode']);

        $result = $this->findLocationById($result['id']);

        $this->assertNotNull($result);
        $this->assertSame('Sulu', $result->getName());
        $this->assertEmpty($result->getStreet());
        $this->assertEmpty($result->getNumber());
        $this->assertEmpty($result->getPostalCode());
        $this->assertEmpty($result->getCity());
        $this->assertEmpty($result->getCountryCode());
    }

    public function testDelete(): void
    {
        $location = $this->createLocation('Symfony');

        /** @var int $locationId */
        $locationId = $location->getId();

        $this->client->jsonRequest('DELETE', '/admin/api/locations/' . $location->getId());

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertHttpStatusCode(204, $response);

        $this->assertNull($this->findLocationById($locationId));
    }
}
