<?php

declare(strict_types=1);

namespace Aegis\Core\Tests\Smoke;

use Aegis\Core\Http\Request;

final class DummyRequest implements Request
{
    /** @var array<string,mixed> */
    private array $attrs = [];

    public function method(): string
    {
        return 'GET';
    }
    public function uri(): string
    {
        return '/';
    }
    public function header(string $name): ?string
    {
        return null;
    }

    public function withAttribute(string $key, mixed $value): Request
    {
        $clone = clone $this;
        $clone->attrs[$key] = $value;
        return $clone;
    }

    public function attribute(string $key, mixed $default = null): mixed
    {
        return $this->attrs[$key] ?? $default;
    }

    public function body(): string
    {
        return '';
    }
}
