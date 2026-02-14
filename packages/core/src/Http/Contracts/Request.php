<?php

declare(strict_types=1);

namespace Aegis\Core\Http\Contracts;

interface Request
{
    public function host(): string;
    public function uri(): string;
    public function method(): string;
    public function header(string $key): ?string;
    public function body(): ?string;

    /**
     * @return array<string, string>
     */
    public function cookies(): array;

    public function querry(string $key, ?string $default = null): ?string;

    public function parameter(string $key, mixed $default = null): mixed;
}
