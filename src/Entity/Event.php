<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 */
class Event
{
    const RESOURCE_KEY = 'events';

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @var \DateTimeImmutable|null
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTimeImmutable|null
     *
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $endDate;

    /**
     * @var Location|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $location;

    /**
     * @var Collection<string, EventTranslation>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\EventTranslation", mappedBy="event", cascade={"ALL"}, indexBy="locale")
     *
     * @Serializer\Exclude
     */
    private $translations;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var MediaInterface|null
     *
     * @ORM\ManyToOne(targetEntity="Sulu\Bundle\MediaBundle\Entity\MediaInterface")
     *
     * @Serializer\Exclude
     */
    private $image = null;

    public function __construct()
    {
        $this->enabled = false;
        $this->translations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @Serializer\VirtualProperty
     */
    public function getLocationId(): ?int
    {
        if (!$this->location) {
            return null;
        }

        return $this->location->getId();
    }

    public function getImage(): ?MediaInterface
    {
        return $this->image;
    }

    /**
     * @return array<string, mixed>|null
     *
     * @Serializer\VirtualProperty
     * @Serializer\SerializedName("image")
     */
    public function getImageData(): ?array
    {
        if (!$this->image) {
            return null;
        }

        return [
            'id' => $this->image->getId(),
        ];
    }

    public function setImage(?MediaInterface $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @Serializer\VirtualProperty(name="title")
     */
    public function getTitle(): ?string
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            return null;
        }

        return $translation->getTitle();
    }

    public function setTitle(string $title): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            $translation = $this->createTranslation($this->locale);
        }

        $translation->setTitle($title);

        return $this;
    }

    /**
     * @Serializer\VirtualProperty(name="teaser")
     */
    public function getTeaser(): ?string
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            return null;
        }

        return $translation->getTeaser();
    }

    public function setTeaser(string $teaser): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            $translation = $this->createTranslation($this->locale);
        }

        $translation->setTeaser($teaser);

        return $this;
    }

    /**
     * @Serializer\VirtualProperty(name="description")
     */
    public function getDescription(): ?string
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            return null;
        }

        return $translation->getDescription();
    }

    public function setDescription(string $description): self
    {
        $translation = $this->getTranslation($this->locale);
        if (!$translation) {
            $translation = $this->createTranslation($this->locale);
        }

        $translation->setDescription($description);

        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return EventTranslation[]
     */
    public function getTranslations(): array
    {
        return $this->translations->toArray();
    }

    protected function getTranslation(string $locale): ?EventTranslation
    {
        if (!$this->translations->containsKey($locale)) {
            return null;
        }

        return $this->translations->get($locale);
    }

    protected function createTranslation(string $locale): EventTranslation
    {
        $translation = new EventTranslation($this, $locale);
        $this->translations->set($locale, $translation);

        return $translation;
    }
}
