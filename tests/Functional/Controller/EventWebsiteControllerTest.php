<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\Traits\EventTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Component\HttpFoundation\Response;

class EventWebsiteControllerTest extends SuluTestCase
{
    use EventTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->purgeDatabase();
        $this->initPhpcr();
    }

    public function testIndexAction(): void
    {
        $client = $this->createWebsiteClient();

        $event = $this->createEvent('Sulu is awesome', 'en');

        $crawler = $client->request('GET', '/en/event/' . $event->getId());

        $response = $client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertHttpStatusCode(200, $response);

        $this->assertStringContainsString('Sulu is awesome', $crawler->filter('h1')->html());
    }
}
