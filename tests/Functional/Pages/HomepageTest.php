<?php

declare(strict_types=1);

namespace App\Tests\Functional\Pages;

use App\Tests\Functional\Traits\EventTrait;
use App\Tests\Functional\Traits\PageTrait;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;
use Sulu\Component\DocumentManager\DocumentManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomepageTest extends SuluTestCase
{
    use EventTrait;
    use PageTrait;

    /**
     * @var KernelBrowser
     */
    private $client;

    public function setUp(): void
    {
        $this->client = $this->createWebsiteClient();
        $this->initPhpcr();
    }

    public function testHomepage(): void
    {
        $event1 = $this->createEvent('Sulu is awesome', 'en');
        $this->enableEvent($event1);
        $event2 = $this->createEvent('Symfony Live is awesome', 'en');
        $this->enableEvent($event2);
        $event3 = $this->createEvent('Disabled', 'en');

        $this->createPage(
            'homepage',
            'example',
            [
                'title' => 'Symfony Live',
                'url' => '/homepage',
                'published' => true,
                'events' => [
                    $event1->getId(),
                    $event2->getId(),
                ],
            ]
        );

        $crawler = $this->client->request(Request::METHOD_GET, '/en/homepage');

        $response = $this->client->getResponse();
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertStringContainsString('Symfony Live', $crawler->filter('h1')->html());
        $this->assertNotNull($content = $crawler->filter('.event-title')->eq(0)->html());
        $this->assertStringContainsString($event1->getTitle() ?: '', $content);
        $this->assertNotNull($content = $crawler->filter('.event-title')->eq(1)->html());
        $this->assertStringContainsString($event2->getTitle() ?: '', $content);
    }

    protected function getDocumentManager(): DocumentManagerInterface
    {
        return $this->getContainer()->get('sulu_document_manager.document_manager');
    }
}
