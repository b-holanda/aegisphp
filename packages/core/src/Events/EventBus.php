<?php

declare(strict_types=1);

namespace Aegis\Core\Events;

interface EventBus
{
    public function dispatch(Event $event): void;

    /**
     * @param class-string<Event> $event
     */
    public function listen(string $event, Listener $listener): void;
}
