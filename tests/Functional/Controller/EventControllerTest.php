<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\Traits\EventTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Component\HttpFoundation\Response;

class EventControllerTest extends SuluTestCase
{
    use EventTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->purgeDatabase();
    }

    public function testCGet(): void
    {
        $client = $this->createAuthenticatedClient();

        $event1 = $this->createEvent('Sulu is awesome', 'de');
        $event2 = $this->createEvent('Symfony live is awesome', 'de');

        $client->request('GET', '/admin/api/events?locale=de');

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent(), true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertSame(2, $result['total']);
        $this->assertCount(2, $result['_embedded']['events']);
        $items = $result['_embedded']['events'];

        $this->assertSame($event1->getId(), $items[0]['id']);
        $this->assertSame($event2->getId(), $items[1]['id']);

        $this->assertSame($event1->getTitle(), $items[0]['title']);
        $this->assertSame($event2->getTitle(), $items[1]['title']);
    }

    public function testGet(): void
    {
        $client = $this->createAuthenticatedClient();

        $event = $this->createEvent('Sulu is awesome', 'de');

        $client->request('GET', '/admin/api/events/' . $event->getId() . '?locale=de');

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent(), true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertSame($event->getId(), $result['id']);
        $this->assertSame($event->getTitle(), $result['title']);
    }

    public function testPost(): void
    {
        $client = $this->createAuthenticatedClient();

        $client->request(
            'POST',
            '/admin/api/events?locale=de',
            [
                'title' => 'Sulu',
                'startDate' => '2019-01-01 12:00',
                'endDate' => '2019-01-02 12:00',
                'description' => 'Sulu is awesome',
            ]
        );

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent(), true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
        $this->assertFalse($result['enabled']);
        $this->assertSame('Sulu', $result['title']);
        $this->assertSame('2019-01-01T12:00:00', $result['startDate']);
        $this->assertSame('2019-01-02T12:00:00', $result['endDate']);
        $this->assertSame('Sulu is awesome', $result['description']);

        $result = $this->findEventById($result['id'], 'de');

        $this->assertNotNull($result);
        $this->assertFalse($result->isEnabled());
        $this->assertSame('Sulu', $result->getTitle());
        $this->assertNotNull($result->getStartDate());
        $this->assertSame('2019-01-01T12:00:00', $result->getStartDate()->format('Y-m-d\TH:i:s'));
        $this->assertNotNull($result->getEndDate());
        $this->assertSame('2019-01-02T12:00:00', $result->getEndDate()->format('Y-m-d\TH:i:s'));
        $this->assertSame('Sulu is awesome', $result->getDescription());
    }

    public function testPut(): void
    {
        $client = $this->createAuthenticatedClient();

        $event = $this->createEvent('Symfony', 'de');

        $client->request(
            'PUT',
            '/admin/api/events/' . $event->getId() . '?locale=de',
            [
                'title' => 'Symfony Live',
                'startDate' => '2019-01-01 12:00',
                'endDate' => '2019-01-02 12:00',
                'description' => 'Symfony Live is awesome',
            ]
        );

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent(), true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
        $this->assertFalse($result['enabled']);
        $this->assertSame('Symfony Live', $result['title']);
        $this->assertSame('2019-01-01T12:00:00', $result['startDate']);
        $this->assertSame('2019-01-02T12:00:00', $result['endDate']);
        $this->assertSame('Symfony Live is awesome', $result['description']);

        $result = $this->findEventById($result['id'], 'de');

        $this->assertNotNull($result);
        $this->assertFalse($result->isEnabled());
        $this->assertSame('Symfony Live', $result->getTitle());
        $this->assertNotNull($result->getStartDate());
        $this->assertSame('2019-01-01T12:00:00', $result->getStartDate()->format('Y-m-d\TH:i:s'));
        $this->assertNotNull($result->getEndDate());
        $this->assertSame('2019-01-02T12:00:00', $result->getEndDate()->format('Y-m-d\TH:i:s'));
        $this->assertSame('Symfony Live is awesome', $result->getDescription());
    }

    public function testEnable(): void
    {
        $client = $this->createAuthenticatedClient();

        $event = $this->createEvent('Symfony', 'de');

        $client->request('POST', '/admin/api/events/' . $event->getId() . '?locale=de&action=enable');

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent(), true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertTrue($result['enabled']);

        $result = $this->findEventById($result['id'], 'de');

        $this->assertNotNull($result);
        $this->assertTrue($result->isEnabled());
    }

    public function testDisable(): void
    {
        $client = $this->createAuthenticatedClient();

        $event = $this->createEvent('Symfony', 'de');
        $this->enableEvent($event);

        $client->request('POST', '/admin/api/events/' . $event->getId() . '?locale=de&action=disable');

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent(), true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertFalse($result['enabled']);

        $result = $this->findEventById($result['id'], 'de');

        $this->assertNotNull($result);
        $this->assertFalse($result->isEnabled());
    }

    public function testDelete(): void
    {
        $client = $this->createAuthenticatedClient();

        $event = $this->createEvent('Symfony', 'de');

        /** @var int $eventId */
        $eventId = $event->getId();

        $client->request('DELETE', '/admin/api/events/' . $event->getId() . '?locale=de');

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertHttpStatusCode(204, $response);

        $this->assertNull($this->findEventById($eventId, 'de'));
    }
}
