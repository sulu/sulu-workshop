<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Event;
use App\Entity\EventTranslation;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class EventTranslationTest extends TestCase
{
    /**
     * @var Event|ObjectProphecy
     */
    private $event;

    /**
     * @var EventTranslation
     */
    private $translation;

    public function setUp(): void
    {
        $this->event = $this->prophesize(Event::class);
        $this->translation = new EventTranslation($this->event->reveal(), 'de');
    }

    public function testEvent(): void
    {
        $this->assertSame($this->event->reveal(), $this->translation->getEvent());
    }

    public function testLocale(): void
    {
        $this->assertSame('de', $this->translation->getLocale());
    }

    public function testTitle(): void
    {
        $this->assertNull($this->translation->getTitle());
        $this->assertSame($this->translation, $this->translation->setTitle('Sulu is awesome'));
        $this->assertSame('Sulu is awesome', $this->translation->getTitle());
    }

    public function testDescription(): void
    {
        $this->assertNull($this->translation->getDescription());
        $this->assertSame($this->translation, $this->translation->setDescription('Sulu is awesome'));
        $this->assertSame('Sulu is awesome', $this->translation->getDescription());
    }
}
