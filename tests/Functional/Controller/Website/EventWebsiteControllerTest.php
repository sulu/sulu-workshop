<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Website;

use App\Tests\Functional\Traits\EventTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class EventWebsiteControllerTest extends SuluTestCase
{
    use EventTrait;

    /**
     * @var KernelBrowser
     */
    private $client;

    public function setUp(): void
    {
        $this->client = $this->createWebsiteClient();
        $this->purgeDatabase();
        $this->initPhpcr();
    }

    public function testIndexAction(): void
    {
        $event = $this->createEvent('Sulu is awesome', 'en');

        $crawler = $this->client->request('GET', '/en/event/' . $event->getId());

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertHttpStatusCode(200, $response);

        $this->assertStringContainsString('Sulu is awesome', $crawler->filter('h1')->html());
    }
}
