<?php

declare(strict_types=1);

namespace Aegis\Core\Tests\Events;

use Aegis\Core\Events\Event;
use Aegis\Core\Events\Listener;
use Aegis\Core\Events\SimpleEventBus;
use PHPUnit\Framework\TestCase;

final class SimpleEventBusTest extends TestCase
{
    public function testDispatchNotifiesListenersRegisteredForEventClass(): void
    {
        $collector = new \stdClass();
        $collector->events = [];

        $listener = new class ($collector) implements Listener {
            public function __construct(private \stdClass $collector)
            {
            }

            public function handle(Event $event): void
            {
                $this->collector->events[] = $event;
            }
        };

        $bus = new SimpleEventBus();
        $bus->listen(UserCreated::class, $listener);

        $event = new UserCreated();
        $bus->dispatch($event);

        $this->assertCount(1, $collector->events);
        $this->assertSame($event, $collector->events[0]);
    }

    public function testDispatchIgnoresOtherEventTypes(): void
    {
        $collector = new \stdClass();
        $collector->events = [];

        $listener = new class ($collector) implements Listener {
            public function __construct(private \stdClass $collector)
            {
            }

            public function handle(Event $event): void
            {
                $this->collector->events[] = $event;
            }
        };

        $bus = new SimpleEventBus();
        $bus->listen(UserCreated::class, $listener);
        $bus->dispatch(new PasswordResetRequested());

        $this->assertCount(0, $collector->events);
    }
}

final class UserCreated implements Event
{
}

final class PasswordResetRequested implements Event
{
}
