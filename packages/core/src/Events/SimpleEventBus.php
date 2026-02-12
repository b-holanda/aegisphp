<?php

declare(strict_types=1);

namespace Aegis\Core\Events;

final class SimpleEventBus implements EventBus
{
    /** @var array<class-string<Event>, list<Listener>> */
    private array $listeners = [];

    public function dispatch(Event $event): void
    {
        $type = $event::class;
        foreach ($this->listeners[$type] ?? [] as $listener) {
            $listener->handle($event);
        }
    }

    public function listen(string $event, Listener $listener): void
    {
        $this->listeners[$event] ??= [];
        $this->listeners[$event][] = $listener;
    }
}
