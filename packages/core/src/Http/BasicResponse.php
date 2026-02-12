<?php

declare(strict_types=1);

namespace Aegis\Core\Http;

final class BasicResponse implements Response
{
    private const DEFAULT_CONTENT_TYPE = 'text/plain; charset=utf-8';

    private readonly int $status;
    /** @var array<string,string> */
    private readonly array $headers;
    private readonly string $body;

    /** @param array<string,string> $headers */
    public function __construct(
        int $status = 200,
        array $headers = [],
        string $body = ''
    ) {
        self::assertValidStatus($status);

        $this->status = $status;
        $this->headers = self::ensureDefaultHeaders($headers);
        $this->body = $body;
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

    /**
     * @return array<string,string>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function write(string $chunk): Response
    {
        return new self($this->status, $this->headers, $this->body . $chunk);
    }

    private static function assertValidStatus(int $code): void
    {
        if ($code < 100 || $code > 599) {
            throw new \InvalidArgumentException('HTTP status code must be between 100 and 599.');
        }
    }

    /**
     * @param array<string,string> $headers
     *
     * @return array<string,string>
     */
    private static function ensureDefaultHeaders(array $headers): array
    {
        foreach ($headers as $name => $_) {
            if (strcasecmp($name, 'Content-Type') === 0) {
                return $headers;
            }
        }

        $headers['Content-Type'] = self::DEFAULT_CONTENT_TYPE;

        return $headers;
    }
}
