<?php

declare(strict_types=1);

namespace Aegis\Core\Http;

use Aegis\Core\Http\Contracts\Request;

class SimpleRequest implements Request
{
    /**
     * @param array<string, string> $headers
     * @param array<string, string> $cookies
     * @param array<string, mixed> $parameters
     * @param array<string, string> $query
     */
    public function __construct(
        private readonly string $host,
        private readonly string $uri,
        private readonly string $method,
        private readonly array $headers = [],
        private readonly array $cookies = [],
        private readonly ?string $body = null,
        private readonly array $parameters = [],
        private readonly array $query = [],
    ) {
    }

    /**
     * @inheritDoc
     */
    public function body(): ?string
    {
        return $this->body;
    }

    /**
     * @inheritDoc
     */
    public function cookies(): array
    {
        return $this->cookies;
    }

    /**
     * @inheritDoc
     */
    public function header(string $key): ?string
    {
        $normalizedKey = strtolower($key);

        if (!\array_key_exists($normalizedKey, $this->headers)) {
            return null;
        }

        $value = $this->headers[$normalizedKey];

        return \is_string($value) ? $value : null;
    }

    /**
     * @inheritDoc
     */
    public function host(): string
    {
        return $this->host;
    }

    /**
     * @inheritDoc
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * @inheritDoc
     */
    public function parameter(string $key, mixed $default = null): mixed
    {
        return $this->parameters[strtolower($key)] ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function querry(string $key, ?string $default = null): ?string
    {
        $normalizedKey = strtolower($key);

        if (!\array_key_exists($normalizedKey, $this->query)) {
            return $default;
        }

        $value = $this->query[$normalizedKey];

        return \is_string($value) ? $value : $default;
    }

    /**
     * @inheritDoc
     */
    public function uri(): string
    {
        return $this->uri;
    }
}
