<?php

declare(strict_types=1);

namespace App\Content;

use App\Entity\Event;
use JMS\Serializer\Annotation as Serializer;
use Sulu\Component\SmartContent\ItemInterface;

class EventDataItem implements ItemInterface
{
    public function __construct(
        /**
         * @Serializer\Exclude
         */
        private readonly Event $entity,
    ) {
    }

    /**
     * @Serializer\VirtualProperty
     */
    public function getId()
    {
        return (string) $this->entity->getId();
    }

    /**
     * @Serializer\VirtualProperty
     */
    public function getTitle()
    {
        return (string) $this->entity->getTitle();
    }

    /**
     * @Serializer\VirtualProperty
     *
     * @return string|null
     */
    public function getImage()
    {
        return null;
    }

    public function getResource(): Event
    {
        return $this->entity;
    }
}
