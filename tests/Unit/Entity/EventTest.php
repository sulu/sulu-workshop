<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Event;
use App\Entity\EventTranslation;
use App\Entity\Location;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;

class EventTest extends TestCase
{
    /**
     * @var Location|ObjectProphecy
     */
    private $location;

    /**
     * @var Event
     */
    private $event;

    public function setUp(): void
    {
        $this->location = $this->prophesize(Location::class);

        $this->event = new Event();
        $this->event->setLocale('de');
    }

    public function testEnabled(): void
    {
        $this->assertFalse($this->event->isEnabled());
        $this->assertSame($this->event, $this->event->setEnabled(true));
        $this->assertTrue($this->event->isEnabled());
    }

    public function testStartDate(): void
    {
        $now = new \DateTimeImmutable();

        $this->assertNull($this->event->getStartDate());
        $this->assertSame($this->event, $this->event->setStartDate($now));
        $this->assertNotNull($this->event->getStartDate());
        $this->assertSame($now, $this->event->getStartDate());
    }

    public function testEndDate(): void
    {
        $now = new \DateTimeImmutable();

        $this->assertNull($this->event->getEndDate());
        $this->assertSame($this->event, $this->event->setEndDate($now));
        $this->assertNotNull($this->event->getEndDate());
        $this->assertSame($now, $this->event->getEndDate());
    }

    public function testLocation(): void
    {
        $this->location->getId()->willReturn(42);

        $this->assertNull($this->event->getLocation());
        $this->assertNull($this->event->getLocationId());
        $this->assertSame($this->event, $this->event->setLocation($this->location->reveal()));
        $this->assertNotNull($this->event->getLocation());
        $this->assertSame($this->location->reveal(), $this->event->getLocation());
        $this->assertSame(42, $this->event->getLocationId());
    }

    public function testImage(): void
    {
        $image = $this->prophesize(MediaInterface::class);
        $image->getId()->willReturn(1234);

        $this->assertNull($this->event->getImage());
        $this->assertNull($this->event->getImageData());
        $this->assertSame($this->event, $this->event->setImage($image->reveal()));
        $this->assertSame($image->reveal(), $this->event->getImage());
        $this->assertSame(['id' => 1234], $this->event->getImageData());
    }

    public function testTitle(): void
    {
        $this->assertNull($this->event->getTitle());
        $this->assertSame($this->event, $this->event->setTitle('Sulu is awesome'));
        $this->assertSame('Sulu is awesome', $this->event->getTitle());

        $this->assertInstanceOf(EventTranslation::class, $this->event->getTranslations()['de']);
        $this->assertSame('de', $this->event->getTranslations()['de']->getLocale());
        $this->assertSame('Sulu is awesome', $this->event->getTranslations()['de']->getTitle());
    }

    public function testTeaser(): void
    {
        $this->assertNull($this->event->getTeaser());
        $this->assertSame($this->event, $this->event->setTeaser('Sulu is awesome'));
        $this->assertSame('Sulu is awesome', $this->event->getTeaser());

        $this->assertInstanceOf(EventTranslation::class, $this->event->getTranslations()['de']);
        $this->assertSame('de', $this->event->getTranslations()['de']->getLocale());
        $this->assertSame('Sulu is awesome', $this->event->getTranslations()['de']->getTeaser());
    }

    public function testDescription(): void
    {
        $this->assertNull($this->event->getDescription());
        $this->assertSame($this->event, $this->event->setDescription('Sulu is awesome'));
        $this->assertSame('Sulu is awesome', $this->event->getDescription());

        $this->assertInstanceOf(EventTranslation::class, $this->event->getTranslations()['de']);
        $this->assertSame('de', $this->event->getTranslations()['de']->getLocale());
        $this->assertSame('Sulu is awesome', $this->event->getTranslations()['de']->getDescription());
    }

    public function testLocale(): void
    {
        $this->assertSame('de', $this->event->getLocale());
        $this->assertSame($this->event, $this->event->setLocale('en'));
        $this->assertSame('en', $this->event->getLocale());
    }
}
