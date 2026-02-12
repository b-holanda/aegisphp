<?php

declare(strict_types=1);

namespace Aegis\Core\Middleware;

interface Pipeline extends Handler
{
    /** @param list<Middleware> $middlewares */
    public function pipe(array $middlewares): self;
}
