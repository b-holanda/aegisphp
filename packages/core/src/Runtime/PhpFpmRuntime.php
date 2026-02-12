<?php

declare(strict_types=1);

namespace Aegis\Core\Runtime;

use Aegis\Core\Http\BasicRequest;
use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;
use Aegis\Core\Middleware\Pipeline;

final class PhpFpmRuntime implements RuntimeStrategy
{
    /** @var callable(int):void */
    private $statusEmitter;

    /** @var callable(string,bool):void */
    private $headerEmitter;

    /** @var callable(string):void */
    private $bodyEmitter;

    /**
     * @param callable(int):void|null $statusEmitter
     * @param callable(string,bool):void|null $headerEmitter
     * @param callable(string):void|null $bodyEmitter
     */
    public function __construct(
        ?callable $statusEmitter = null,
        ?callable $headerEmitter = null,
        ?callable $bodyEmitter = null
    ) {
        $this->statusEmitter = $statusEmitter ?? static function (int $status): void {
            http_response_code($status);
        };
        $this->headerEmitter = $headerEmitter ?? static function (string $line, bool $replace): void {
            header($line, $replace);
        };
        $this->bodyEmitter = $bodyEmitter ?? static function (string $chunk): void {
            echo $chunk;
        };
    }

    public function run(Pipeline $app): void
    {
        $req = $this->toRequest(null);
        $res = $app->handle($req);
        $this->emit($res);
    }

    public function toRequest(mixed $native): Request
    {
        $method = isset($_SERVER['REQUEST_METHOD']) && \is_string($_SERVER['REQUEST_METHOD'])
            ? $_SERVER['REQUEST_METHOD']
            : 'GET';
        $uri = isset($_SERVER['REQUEST_URI']) && \is_string($_SERVER['REQUEST_URI'])
            ? $_SERVER['REQUEST_URI']
            : '/';
        $body = (string) file_get_contents('php://input');

        $headers = [];
        foreach ($_SERVER as $k => $v) {
            if (!\is_string($v)) {
                continue;
            }

            if (str_starts_with($k, 'HTTP_')) {
                $name = str_replace('_', '-', strtolower(substr($k, 5)));
                $headers[$name] = $v;
            }
        }

        // CONTENT_TYPE e CONTENT_LENGTH não vêm com prefixo HTTP_ no php-fpm.
        foreach (['CONTENT_TYPE', 'CONTENT_LENGTH'] as $serverKey) {
            if (!isset($_SERVER[$serverKey]) || !\is_string($_SERVER[$serverKey])) {
                continue;
            }

            $headers[strtolower(str_replace('_', '-', $serverKey))] = $_SERVER[$serverKey];
        }

        $req = new BasicRequest($method, $uri, $headers, $body);

        // extras úteis para middlewares (rate limit etc.)
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        return $req->withAttribute('client_ip', $ip);
    }

    public function emit(Response $response): void
    {
        ($this->statusEmitter)($response->status());

        $headers = $this->normalizeHeaders($response->headers());

        foreach ($headers as $name => $value) {
            ($this->headerEmitter)($name . ': ' . $value, true);
        }

        ($this->bodyEmitter)($response->body());
    }

    /** @param array<string,string> $headers */
    private function hasHeader(array $headers, string $needle): bool
    {
        foreach ($headers as $name => $_) {
            if (strcasecmp($name, $needle) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array<string,string> $headers
     *
     * @return array<string,string>
     */
    private function normalizeHeaders(array $headers): array
    {
        if (!$this->hasHeader($headers, 'Content-Type')) {
            $headers['Content-Type'] = 'text/plain; charset=utf-8';
        }

        return $headers;
    }
}
