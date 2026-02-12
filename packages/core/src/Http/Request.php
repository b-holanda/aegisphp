<?php

declare(strict_types=1);

namespace Aegis\Core\Http;

interface Request
{
    public function method(): string;
    public function uri(): string;

    public function header(string $name): ?string;

    public function withAttribute(string $key, mixed $value): self;
    public function attribute(string $key, mixed $default = null): mixed;

    public function body(): string;
}
