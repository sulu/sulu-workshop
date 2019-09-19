<?php

declare(strict_types=1);

namespace App\Content;

use App\Entity\Event;
use JMS\Serializer\Annotation as Serializer;
use Sulu\Component\SmartContent\ItemInterface;

class EventDataItem implements ItemInterface
{
    /**
     * @var Event
     *
     * @Serializer\Exclude
     */
    private $entity;

    public function __construct(Event $entity)
    {
        $this->entity = $entity;
    }

    /**
     * @Serializer\VirtualProperty
     */
    public function getId()
    {
        return $this->entity->getId();
    }

    /**
     * @Serializer\VirtualProperty
     */
    public function getTitle()
    {
        return $this->entity->getTitle();
    }

    /**
     * @Serializer\VirtualProperty
     */
    public function getImage()
    {
        return null;
    }

    public function getResource()
    {
        return $this->entity;
    }
}
