<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Admin;

use App\Tests\Functional\Traits\LocationTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

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
}
