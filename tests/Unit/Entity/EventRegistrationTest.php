<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Event;
use App\Entity\EventRegistration;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class EventRegistrationTest extends TestCase
{
    /**
     * @var Event|ObjectProphecy
     */
    private $event;

    /**
     * @var EventRegistration
     */
    private $eventRegistration;

    public function setUp(): void
    {
        $this->event = $this->prophesize(Event::class);
        $this->eventRegistration = new EventRegistration($this->event->reveal());
    }

    public function testFirstName(): void
    {
        $this->assertSame($this->eventRegistration, $this->eventRegistration->setFirstName('Max'));
        $this->assertSame('Max', $this->eventRegistration->getFirstName());
    }

    public function testGetLastName(): void
    {
        $this->assertSame($this->eventRegistration, $this->eventRegistration->setLastName('Mustermann'));
        $this->assertSame('Mustermann', $this->eventRegistration->getLastName());
    }

    public function testGetEmail(): void
    {
        $this->assertSame($this->eventRegistration, $this->eventRegistration->setEmail('max@mustermann.at'));
        $this->assertSame('max@mustermann.at', $this->eventRegistration->getEmail());
    }

    public function testGetMessage(): void
    {
        $this->assertSame($this->eventRegistration, $this->eventRegistration->setMessage('I would live to see this.'));
        $this->assertSame('I would live to see this.', $this->eventRegistration->getMessage());
    }
}
