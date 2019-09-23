<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Location;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    /**
     * @var Location
     */
    private $location;

    public function setUp(): void
    {
        $this->location = new Location();
    }

    public function testName(): void
    {
        $this->assertNull($this->location->getName());
        $this->assertSame($this->location, $this->location->setName('Sulu GmbH'));
        $this->assertNotNull($this->location->getName());
        $this->assertSame('Sulu GmbH', $this->location->getName());
    }

    public function testStreet(): void
    {
        $this->assertNull($this->location->getStreet());
        $this->assertSame($this->location, $this->location->setStreet('Teststreet'));
        $this->assertNotNull($this->location->getStreet());
        $this->assertSame('Teststreet', $this->location->getStreet());
    }

    public function testNumber(): void
    {
        $this->assertNull($this->location->getNumber());
        $this->assertSame($this->location, $this->location->setNumber('42'));
        $this->assertNotNull($this->location->getNumber());
        $this->assertSame('42', $this->location->getNumber());
    }

    public function testPostalCode(): void
    {
        $this->assertNull($this->location->getPostalCode());
        $this->assertSame($this->location, $this->location->setPostalCode('6850'));
        $this->assertNotNull($this->location->getPostalCode());
        $this->assertSame('6850', $this->location->getPostalCode());
    }

    public function testCity(): void
    {
        $this->assertNull($this->location->getCity());
        $this->assertSame($this->location, $this->location->setCity('Dornbirn'));
        $this->assertNotNull($this->location->getCity());
        $this->assertSame('Dornbirn', $this->location->getCity());
    }

    public function testCountryCode(): void
    {
        $this->assertNull($this->location->getCountryCode());
        $this->assertSame($this->location, $this->location->setCountryCode('AT'));
        $this->assertNotNull($this->location->getCountryCode());
        $this->assertSame('AT', $this->location->getCountryCode());
    }
}
