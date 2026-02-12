<?php

declare(strict_types=1);

namespace Aegis\Core\Config;

final class ArrayConfig implements Config
{
    /** @param array<string,mixed> $data */
    public function __construct(private readonly array $data)
    {
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key);
        $cur = $this->data;

        foreach ($parts as $p) {
            if (!\is_array($cur) || !\array_key_exists($p, $cur)) {
                return $default;
            }
            $cur = $cur[$p];
        }

        return $cur;
    }
}
