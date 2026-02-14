<?php

declare(strict_types=1);

namespace Aegis\Core\Http\Contracts;

interface Response
{
    public function status(): int;

    /**
     * @return array<string, string>
     */
    public function headers(): array;

    public function body(): string;

    public function withStatus(int $status): self;

    /**
     * @param array<string, string> $headers
     */
    public function withHeaders(array $headers): self;

    public function write(string $chunk): self;
}
