<?php

declare(strict_types=1);

namespace Aegis\Core\Config;

interface ConfigLoader
{
    /**
     * @param list<string|array<string,mixed>> $sources
     */
    public function load(array $sources): Config;
}
