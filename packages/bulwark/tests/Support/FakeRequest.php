<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Tests\Support;

use Aegis\Core\Http\Request;

final class FakeRequest implements Request
{
    /** @var array<string,mixed> */
    private array $attributes;

    /**
     * @param array<string,string> $headers
     * @param array<string,mixed> $attributes
     */
    public function __construct(
        private readonly string $method = 'GET',
        private readonly string $uri = '/',
        private readonly array $headers = [],
        private readonly string $body = '',
        array $attributes = []
    ) {
        $this->attributes = $attributes;
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
        foreach ($this->headers as $header => $value) {
            if (strtolower($header) === $needle) {
                return $value;
            }
        }

        return null;
    }

    public function withAttribute(string $key, mixed $value): Request
    {
        $clone = clone $this;
        $clone->attributes[$key] = $value;

        return $clone;
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
