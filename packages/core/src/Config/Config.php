<?php

declare(strict_types=1);

namespace Aegis\Core\Config;

interface Config
{
    public function get(string $key, mixed $default = null): mixed;
}
