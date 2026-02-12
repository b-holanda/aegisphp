<?php

declare(strict_types=1);

namespace Aegis\Core\Config;

interface Env
{
    public function get(string $key, ?string $default = null): ?string;

    public function require(string $key): string;
}
