<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Http;

use Aegis\Core\Http\Response;

final class BulwarkResponse implements Response
{
    /** @var array<string,string> */
    private readonly array $headers;

    /** @param array<string,string> $headers */
    public function __construct(
        private readonly int $status = 200,
        array $headers = [],
        private readonly string $body = ''
    ) {
        if ($status < 100 || $status > 599) {
            throw new \InvalidArgumentException('HTTP status code must be between 100 and 599.');
        }

        $this->headers = $headers;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function withStatus(int $code): Response
    {
        return new self($code, $this->headers, $this->body);
    }

    public function header(string $name, string $value): Response
    {
        $next = $this->headers;
        foreach ($next as $existingName => $_) {
            if (strcasecmp($existingName, $name) === 0) {
                unset($next[$existingName]);
            }
        }

        $next[$name] = $value;

        return new self($this->status, $next, $this->body);
    }

    public function body(): string
    {
        return $this->body;
    }

    public function write(string $chunk): Response
    {
        return new self($this->status, $this->headers, $this->body . $chunk);
    }

    /**
     * @return array<string,string>
     */
    public function headers(): array
    {
        return $this->headers;
    }
}
