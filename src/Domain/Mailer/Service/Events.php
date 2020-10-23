<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Zentlix to newer
 * versions in the future. If you wish to customize Zentlix for your
 * needs please refer to https://docs.zentlix.io for more information.
 */

declare(strict_types=1);

namespace Zentlix\UserBundle\Domain\Mailer\Service;

use Zentlix\UserBundle\Infrastructure\Mailer\Event\EventInterface;

class Events
{
    /** @var EventInterface[] */
    private array $events = [];

    public function __construct(iterable $events)
    {
        foreach ($events as $event) {
            $this->addEvent($event);
        }
    }

    public function addEvent(EventInterface $event)
    {
        if(isset($this->events[get_class($event)])) {
            throw new \DomainException(sprintf('Event class %s already exist.', get_class($event)));
        }

        $this->events[get_class($event)] = $event;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public function find(string $class): ?EventInterface
    {
        if(isset($this->events[$class])) {
            return $this->events[$class];
        }

        return null;
    }

    public function get(string $class): EventInterface
    {
        return $this->events[$class];
    }

    public function assoc(): array
    {
        $events = [];
        foreach ($this->events as $event) {
            $events[$event->getTitle()] = get_class($event);
        }

        return $events;
    }
}