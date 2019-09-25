<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\Traits\EventRegistrationTrait;
use App\Tests\Functional\Traits\EventTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Symfony\Component\HttpFoundation\Response;

class EventWebsiteControllerTest extends SuluTestCase
{
    use EventTrait;
    use EventRegistrationTrait;

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

    public function testRegister(): void
    {
        $client = $this->createWebsiteClient();

        $event = $this->createEvent('Sulu is awesome', 'en');

        $crawler = $client->request('GET', '/en/event/' . $event->getId());

        $response = $client->getResponse();
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

        $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertStringContainsString('Thanks for your registration', $crawler->filter('.success')->html());

        $registrations = $this->findEventRegistrations($event);

        $this->assertCount(1, $registrations);
        $this->assertSame('Max', $registrations[0]->getFirstName());
        $this->assertSame('Mustermann', $registrations[0]->getLastName());
        $this->assertSame('max@mustermann.at', $registrations[0]->getEmail());
        $this->assertSame('I would love to see this.', $registrations[0]->getMessage());
    }
}
