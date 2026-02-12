<?php

declare(strict_types=1);

namespace Aegis\Core\Http;

final class BasicRequest implements Request
{
    /** @param array<string,string> $headers */
    public function __construct(
        private readonly string $method,
        private readonly string $uri,
        private readonly array $headers = [],
        private readonly string $body = '',
        /** @var array<string,mixed> */
        private readonly array $attributes = []
    ) {
    }

    public function method(): string
    {
        return $this->method;
    }
    public function uri(): string
    {
        return $this->uri;
    }

    public function header(string $name): ?string
    {
        $needle = strtolower($name);
        foreach ($this->headers as $k => $v) {
            if (strtolower($k) === $needle) {
                return $v;
            }
        }
        return null;
    }

    public function withAttribute(string $key, mixed $value): Request
    {
        $next = $this->attributes;
        $next[$key] = $value;

        return new self($this->method, $this->uri, $this->headers, $this->body, $next);
    }

    public function attribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    public function body(): string
    {
        return $this->body;
    }
}
