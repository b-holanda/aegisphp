<?php

declare(strict_types=1);

namespace Aegis\Core\Tests\Runtime;

use Aegis\Core\Http\Response;
use Aegis\Core\Runtime\PhpFpmRuntime;
use PHPUnit\Framework\TestCase;

final class PhpFpmRuntimeTest extends TestCase
{
    /** @var array<string,mixed> */
    private array $serverBackup = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->serverBackup = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverBackup;
        parent::tearDown();
    }

    public function testToRequestMapsMethodUriHeadersAndClientIp(): void
    {
        $_SERVER = [
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => '/users',
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_AUTHORIZATION' => 'Bearer token',
            'CONTENT_TYPE' => 'application/json',
            'CONTENT_LENGTH' => '21',
        ];

        $runtime = new PhpFpmRuntime();
        $request = $runtime->toRequest(null);

        $this->assertSame('POST', $request->method());
        $this->assertSame('/users', $request->uri());
        $this->assertSame('Bearer token', $request->header('authorization'));
        $this->assertSame('application/json', $request->header('content-type'));
        $this->assertSame('21', $request->header('content-length'));
        $this->assertSame('127.0.0.1', $request->attribute('client_ip'));
    }

    public function testToRequestAppliesSafeDefaultsWhenServerDataIsMissing(): void
    {
        $_SERVER = [];

        $runtime = new PhpFpmRuntime();
        $request = $runtime->toRequest(null);

        $this->assertSame('GET', $request->method());
        $this->assertSame('/', $request->uri());
        $this->assertSame('0.0.0.0', $request->attribute('client_ip'));
    }

    public function testEmitAddsDefaultContentTypeWhenResponseDoesNotProvideIt(): void
    {
        $statusCode = 0;
        $headerLines = [];
        $replaceFlags = [];
        $emittedBody = '';

        $runtime = new PhpFpmRuntime(
            static function (int $status) use (&$statusCode): void {
                $statusCode = $status;
            },
            static function (string $line, bool $replace) use (&$headerLines, &$replaceFlags): void {
                $headerLines[] = $line;
                $replaceFlags[] = $replace;
            },
            static function (string $chunk) use (&$emittedBody): void {
                $emittedBody .= $chunk;
            }
        );

        $response = new class () implements Response {
            public function __construct(
                private readonly int $status = 202,
                /** @var array<string,string> */
                private readonly array $headers = [],
                private readonly string $body = 'runtime-body'
            ) {
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

            public function headers(): array
            {
                return $this->headers;
            }
        };

        $runtime->emit($response);

        $this->assertSame(202, $statusCode);
        $this->assertContains('Content-Type: text/plain; charset=utf-8', $headerLines);
        $this->assertSame('runtime-body', $emittedBody);
        $this->assertSame([true], $replaceFlags);
    }
}
