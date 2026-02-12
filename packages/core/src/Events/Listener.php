<?php

declare(strict_types=1);

namespace Aegis\Core\Events;

interface Listener
{
    public function handle(Event $event): void;
}
