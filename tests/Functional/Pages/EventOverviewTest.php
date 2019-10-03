<?php

declare(strict_types=1);

namespace App\Tests\Functional\Pages;

use App\Tests\Functional\Traits\EventTrait;
use App\Tests\Functional\Traits\LocationTrait;
use App\Tests\Functional\Traits\PageTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventOverviewTest extends SuluTestCase
{
    use EventTrait;
    use LocationTrait;
    use PageTrait;

    /**
     * @var KernelBrowser
     */
    private $client;

    public function setUp(): void
    {
        $this->client = $this->createWebsiteClient();
        $this->initPhpcr();
        $this->purgeDatabase();
    }

    public function testEventOverview(): void
    {
        $event1 = $this->createEvent('Sulu is awesome', 'en');
        $this->enableEvent($event1);
        $event2 = $this->createEvent('Symfony Live is awesome', 'en');
        $this->enableEvent($event2);
        $event3 = $this->createEvent('Disabled', 'en');

        $this->createPage(
            'event_overview',
            'example',
            [
                'title' => 'Symfony Live',
                'url' => '/events',
                'published' => true,
            ]
        );

        $crawler = $this->client->request(Request::METHOD_GET, '/en/events');

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertStringContainsString('Symfony Live', $crawler->filter('h1')->html());
        $this->assertNotNull($content = $crawler->filter('.event-title')->eq(0)->html());
        $this->assertStringContainsString($event1->getTitle() ?: '', $content);
        $this->assertNotNull($content = $crawler->filter('.event-title')->eq(1)->html());
        $this->assertStringContainsString($event2->getTitle() ?: '', $content);
    }

    public function testEventOverviewWithLocations(): void
    {
        $location1 = $this->createLocation('Dornbirn');
        $location2 = $this->createLocation('Berlin');

        $event1 = $this->createEvent('Sulu is awesome', 'en');
        $event1->setLocation($location1);
        $this->enableEvent($event1);
        $event2 = $this->createEvent('Symfony Live is awesome', 'en');
        $event2->setLocation($location2);
        $this->enableEvent($event2);
        $event3 = $this->createEvent('Disabled', 'en');

        $this->createPage(
            'event_overview',
            'example',
            [
                'title' => 'Symfony Live',
                'url' => '/events',
                'published' => true,
            ]
        );

        $crawler = $this->client->request(Request::METHOD_GET, '/en/events');

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertCount(2, $crawler->filter('.event-title'));
        $this->assertNotNull($content = $crawler->filter('.event-title')->eq(0)->html());
        $this->assertStringContainsString($event1->getTitle() ?: '', $content);
        $this->assertNotNull($content = $crawler->filter('.event-title')->eq(1)->html());
        $this->assertStringContainsString($event2->getTitle() ?: '', $content);

        $form = $crawler->filter('#location_submit')->form(
            [
                'location' => $location1->getId(),
            ]
        );

        $crawler = $this->client->submit($form);

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertCount(1, $crawler->filter('.event-title'));
        $this->assertNotNull($content = $crawler->filter('.event-title')->eq(0)->html());
        $this->assertStringContainsString($event1->getTitle() ?: '', $content);
    }

    protected function getDocumentManager(): DocumentManagerInterface
    {
        return $this->getContainer()->get('sulu_document_manager.document_manager');
    }
}
