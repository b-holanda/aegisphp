<?php

declare(strict_types=1);

namespace Aegis\Core\Config;

final class NativeEnv implements Env
{
    /** @param array<string,scalar|null> $values */
    public function __construct(private readonly array $values = [])
    {
    }

    public function get(string $key, ?string $default = null): ?string
    {
        if (\array_key_exists($key, $this->values)) {
            return $this->toStringOrNull($this->values[$key]) ?? $default;
        }

        if (\array_key_exists($key, $_ENV)) {
            $value = $this->toStringOrNull($_ENV[$key]);
            if ($value !== null) {
                return $value;
            }
        }

        if (\array_key_exists($key, $_SERVER)) {
            $value = $this->toStringOrNull($_SERVER[$key]);
            if ($value !== null) {
                return $value;
            }
        }

        $value = getenv($key);

        return \is_string($value) ? $value : $default;
    }

    public function require(string $key): string
    {
        $value = $this->get($key, null);
        if ($value === null || $value === '') {
            throw new \RuntimeException("Missing required env var: {$key}");
        }

        return $value;
    }

    private function toStringOrNull(mixed $value): ?string
    {
        return \is_scalar($value) ? (string) $value : null;
    }
}
