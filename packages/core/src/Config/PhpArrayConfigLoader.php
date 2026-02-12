<?php

declare(strict_types=1);

namespace Aegis\Core\Config;

final class PhpArrayConfigLoader implements ConfigLoader
{
    public function load(array $sources): Config
    {
        $merged = [];

        foreach ($sources as $src) {
            if (\is_string($src)) {
                /** @var mixed $loaded */
                $loaded = require $src;
                if (!\is_array($loaded)) {
                    throw new \RuntimeException("Config source must return array: {$src}");
                }
                $merged = array_replace_recursive($merged, $loaded);
                continue;
            }

            if (\is_array($src)) {
                $merged = array_replace_recursive($merged, $src);
                continue;
            }

            throw new \InvalidArgumentException('Config source must be string path or array.');
        }

        /** @var array<string,mixed> $merged */
        return new ArrayConfig($merged);
    }
}
