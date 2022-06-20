<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Admin;

use App\Tests\Functional\Traits\EventTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class EventControllerTest extends SuluTestCase
{
    use EventTrait;

    /**
     * @var KernelBrowser
     */
    private $client;

    protected function setUp(): void
    {
        $this->client = $this->createAuthenticatedClient();
        $this->purgeDatabase();
    }

    public function testGetList(): void
    {
        $event1 = $this->createEvent('Sulu is awesome', 'de');
        $event2 = $this->createEvent('Symfony live is awesome', 'de');

        $this->client->jsonRequest('GET', '/admin/api/events?locale=de');

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent() ?: '', true);
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
        $event = $this->createEvent('Sulu is awesome', 'de');

        $this->client->jsonRequest('GET', '/admin/api/events/' . $event->getId() . '?locale=de');

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent() ?: '', true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertSame($event->getId(), $result['id']);
        $this->assertSame($event->getTitle(), $result['title']);
    }

    public function testPost(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/admin/api/events?locale=de',
            [
                'title' => 'Sulu',
                'teaser' => 'Sulu is awesome',
                'startDate' => '2019-01-01 12:00',
                'endDate' => '2019-01-02 12:00',
                'description' => 'Sulu is really awesome',
                'location' => 'Dornbirn',
            ]
        );

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent() ?: '', true);
        $this->assertHttpStatusCode(201, $response);

        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
        $this->assertFalse($result['enabled']);
        $this->assertSame('Sulu', $result['title']);
        $this->assertSame('Sulu is awesome', $result['teaser']);
        $this->assertSame('2019-01-01T12:00:00+00:00', $result['startDate']);
        $this->assertSame('2019-01-02T12:00:00+00:00', $result['endDate']);
        $this->assertSame('Sulu is really awesome', $result['description']);
        $this->assertSame('Dornbirn', $result['location']);

        $result = $this->findEventById($result['id'], 'de');

        $this->assertNotNull($result);
        $this->assertFalse($result->isEnabled());
        $this->assertSame('Sulu', $result->getTitle());
        $this->assertSame('Sulu is awesome', $result->getTeaser());
        $this->assertNotNull($result->getStartDate());
        $this->assertSame('2019-01-01T12:00:00+00:00', $result->getStartDate()->format('c'));
        $this->assertNotNull($result->getEndDate());
        $this->assertSame('2019-01-02T12:00:00+00:00', $result->getEndDate()->format('c'));
        $this->assertSame('Sulu is really awesome', $result->getDescription());
        $this->assertSame('Dornbirn', $result->getLocation());
    }

    public function testPostNullValues(): void
    {
        $this->client->jsonRequest(
            'POST',
            '/admin/api/events?locale=de',
            [
                'title' => 'Sulu',
                'teaser' => null,
                'startDate' => null,
                'endDate' => null,
                'description' => null,
                'location' => null,
            ]
        );

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent() ?: '', true);
        $this->assertHttpStatusCode(201, $response);

        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
        $this->assertFalse($result['enabled']);
        $this->assertSame('Sulu', $result['title']);
        $this->assertSame($result['teaser'], '');
        $this->assertNull($result['startDate']);
        $this->assertNull($result['endDate']);
        $this->assertSame($result['description'], '');
        $this->assertNull($result['location']);

        $result = $this->findEventById($result['id'], 'de');

        $this->assertNotNull($result);
        $this->assertFalse($result->isEnabled());
        $this->assertSame('Sulu', $result->getTitle());
        $this->assertSame($result->getTeaser(), '');
        $this->assertNull($result->getStartDate());
        $this->assertNull($result->getEndDate());
        $this->assertSame($result->getDescription(), '');
        $this->assertNull($result->getLocation());
    }

    public function testPut(): void
    {
        $event = $this->createEvent('Symfony', 'de');

        $this->client->jsonRequest(
            'PUT',
            '/admin/api/events/' . $event->getId() . '?locale=de',
            [
                'title' => 'Symfony Live',
                'teaser' => 'Symfony Live is awesome',
                'startDate' => '2019-01-01 12:00',
                'endDate' => '2019-01-02 12:00',
                'description' => 'Symfony Live is really awesome',
                'location' => 'Dornbirn',
            ]
        );

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent() ?: '', true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
        $this->assertFalse($result['enabled']);
        $this->assertSame('Symfony Live', $result['title']);
        $this->assertSame('Symfony Live is awesome', $result['teaser']);
        $this->assertSame('2019-01-01T12:00:00+00:00', $result['startDate']);
        $this->assertSame('2019-01-02T12:00:00+00:00', $result['endDate']);
        $this->assertSame('Symfony Live is really awesome', $result['description']);
        $this->assertSame('Dornbirn', $result['location']);

        $result = $this->findEventById($result['id'], 'de');

        $this->assertNotNull($result);
        $this->assertFalse($result->isEnabled());
        $this->assertSame('Symfony Live', $result->getTitle());
        $this->assertSame('Symfony Live is awesome', $result->getTeaser());
        $this->assertNotNull($result->getStartDate());
        $this->assertSame('2019-01-01T12:00:00+00:00', $result->getStartDate()->format('c'));
        $this->assertNotNull($result->getEndDate());
        $this->assertSame('2019-01-02T12:00:00+00:00', $result->getEndDate()->format('c'));
        $this->assertSame('Symfony Live is really awesome', $result->getDescription());
        $this->assertSame('Dornbirn', $result->getLocation());
    }

    public function testPutNullValues(): void
    {
        $event = $this->createEvent('Symfony', 'de');

        $this->client->jsonRequest(
            'PUT',
            '/admin/api/events/' . $event->getId() . '?locale=de',
            [
                'title' => 'Symfony Live',
                'teaser' => null,
                'startDate' => null,
                'endDate' => null,
                'description' => null,
                'location' => null,
            ]
        );

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent() ?: '', true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertArrayHasKey('id', $result);
        $this->assertNotNull($result['id']);
        $this->assertFalse($result['enabled']);
        $this->assertSame('Symfony Live', $result['title']);
        $this->assertSame($result['teaser'], '');
        $this->assertNull($result['startDate']);
        $this->assertNull($result['endDate']);
        $this->assertSame($result['description'], '');
        $this->assertNull($result['location']);

        $result = $this->findEventById($result['id'], 'de');

        $this->assertNotNull($result);
        $this->assertFalse($result->isEnabled());
        $this->assertSame('Symfony Live', $result->getTitle());
        $this->assertSame($result->getTeaser(), '');
        $this->assertNull($result->getStartDate());
        $this->assertNull($result->getEndDate());
        $this->assertSame($result->getDescription(), '');
        $this->assertNull($result->getLocation());
    }

    public function testEnable(): void
    {
        $event = $this->createEvent('Symfony', 'de');

        $this->client->jsonRequest('POST', '/admin/api/events/' . $event->getId() . '?locale=de&action=enable');

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent() ?: '', true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertTrue($result['enabled']);

        $result = $this->findEventById($result['id'], 'de');

        $this->assertNotNull($result);
        $this->assertTrue($result->isEnabled());
    }

    public function testDisable(): void
    {
        $event = $this->createEvent('Symfony', 'de');
        $this->enableEvent($event);

        $this->client->jsonRequest('POST', '/admin/api/events/' . $event->getId() . '?locale=de&action=disable');

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $result = json_decode($response->getContent() ?: '', true);
        $this->assertHttpStatusCode(200, $response);

        $this->assertFalse($result['enabled']);

        $result = $this->findEventById($result['id'], 'de');

        $this->assertNotNull($result);
        $this->assertFalse($result->isEnabled());
    }

    public function testDelete(): void
    {
        $event = $this->createEvent('Symfony', 'de');

        /** @var int $eventId */
        $eventId = $event->getId();

        $this->client->jsonRequest('DELETE', '/admin/api/events/' . $event->getId() . '?locale=de');

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertHttpStatusCode(204, $response);

        $this->assertNull($this->findEventById($eventId, 'de'));
    }
}
