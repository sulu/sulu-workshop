<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Website;

use App\Tests\Functional\Traits\EventRegistrationTrait;
use App\Tests\Functional\Traits\EventTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class EventWebsiteControllerTest extends SuluTestCase
{
    use EventTrait;
    use EventRegistrationTrait;

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

    public function testRegister(): void
    {
        $event = $this->createEvent('Sulu is awesome', 'en');

        $crawler = $this->client->request('GET', '/en/event/' . $event->getId());

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertHttpStatusCode(200, $response);

        $form = $crawler->filter('#event_registration_submit')->form(
            [
                'event_registration[firstName]' => 'Max',
                'event_registration[lastName]' => 'Mustermann',
                'event_registration[email]' => 'max@mustermann.at',
                'event_registration[message]' => 'I would love to see this.',
            ]
        );

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();

        $this->assertStringContainsString('Thanks for your registration', $crawler->filter('.success')->html());

        $registrations = $this->findEventRegistrations($event);

        $this->assertCount(1, $registrations);
        $this->assertSame('Max', $registrations[0]->getFirstName());
        $this->assertSame('Mustermann', $registrations[0]->getLastName());
        $this->assertSame('max@mustermann.at', $registrations[0]->getEmail());
        $this->assertSame('I would love to see this.', $registrations[0]->getMessage());
    }
}
