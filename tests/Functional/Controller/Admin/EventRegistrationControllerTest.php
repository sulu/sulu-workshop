<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Admin;

use App\Tests\Functional\Traits\EventRegistrationTrait;
use App\Tests\Functional\Traits\EventTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class EventRegistrationControllerTest extends SuluTestCase
{
    use EventRegistrationTrait;
    use EventTrait;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = $this->createAuthenticatedClient();
        $this->purgeDatabase();
    }

    public function testGetList(): void
    {
        $event1 = $this->createEvent('Sulu is awesome', 'de');
        $event2 = $this->createEvent('Symfony live is awesome', 'de');

        $registration1 = $this->createEventRegistration($event1, 'Max', 'Mustermann');
        $registration2 = $this->createEventRegistration($event2, 'Mira', 'Musterfrau');

        $this->client->jsonRequest('GET', '/admin/api/events/' . $event1->getId() . '/registrations');

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        /**
         * @var array{
         *     _embedded: array{
         *         event_registrations: array<array{
         *             id: int,
         *             firstName: string,
         *             lastName: string,
         *             email: string,
         *         }>
         *     },
         *     total: int,
         * } $result
         */
        $result = \json_decode($response->getContent() ?: '', true, 512, \JSON_THROW_ON_ERROR);
        $this->assertHttpStatusCode(200, $response);

        $this->assertSame(1, $result['total']);
        $this->assertCount(1, $result['_embedded']['event_registrations']);
        $items = $result['_embedded']['event_registrations'];

        $this->assertSame($registration1->getId(), $items[0]['id']);

        $this->assertSame($registration1->getFirstName(), $items[0]['firstName']);
        $this->assertSame($registration1->getLastName(), $items[0]['lastName']);
        $this->assertArrayHasKey('email', $items[0]);
    }
}
