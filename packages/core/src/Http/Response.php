<?php

declare(strict_types=1);

namespace Aegis\Core\Http;

interface Response
{
    public function status(): int;
    public function withStatus(int $code): self;

    public function header(string $name, string $value): self;

    /** Retorna o body completo (string) */
    public function body(): string;

    /** Retorna uma nova instância com conteúdo anexado */
    public function write(string $chunk): self;

    /**
     * Headers normalizados.
     *
     * @return array<string,string>
     */
    public function headers(): array;
}
