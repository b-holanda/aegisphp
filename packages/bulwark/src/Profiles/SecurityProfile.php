<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Profiles;

use Aegis\Core\Middleware\Middleware;

interface SecurityProfile
{
    /** @return list<Middleware> */
    public function middlewares(): array;
}
