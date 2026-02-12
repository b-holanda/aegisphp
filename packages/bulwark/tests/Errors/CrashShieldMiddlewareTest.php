<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Tests\Errors;

use Aegis\Bulwark\Errors\CrashShieldMiddleware;
use Aegis\Bulwark\Tests\Support\FakeRequest;
use Aegis\Core\Http\BasicResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;
use Aegis\Core\Middleware\CallableHandler;
use Aegis\Core\Middleware\Handler;
use PHPUnit\Framework\TestCase;

final class CrashShieldMiddlewareTest extends TestCase
{
    public function testPassesThroughWhenNoExceptionHappens(): void
    {
        $middleware = new CrashShieldMiddleware(false);
        $request = new FakeRequest();

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => (new BasicResponse(200))->write('ok'))
        );

        $this->assertSame(200, $response->status());
        $this->assertSame('ok', $response->body());
    }

    public function testReturnsGenericMessageInProductionMode(): void
    {
        $middleware = new CrashShieldMiddleware(false);
        $request = new FakeRequest();

        $response = $middleware->process(
            $request,
            $this->throwingHandler('db password leaked')
        );

        $this->assertSame(500, $response->status());
        $this->assertSame('Internal Server Error', $response->body());
        $this->assertSame('text/plain; charset=utf-8', $this->header($response->headers(), 'Content-Type'));
        $this->assertStringNotContainsString('db password leaked', $response->body());
    }

    public function testIncludesExceptionMessageInDebugMode(): void
    {
        $middleware = new CrashShieldMiddleware(true);
        $request = new FakeRequest();

        $response = $middleware->process(
            $request,
            $this->throwingHandler('boom')
        );

        $this->assertSame(500, $response->status());
        $this->assertStringContainsString('Internal Server Error', $response->body());
        $this->assertStringContainsString('boom', $response->body());
    }

    /**
     * @param array<string,string> $headers
     */
    private function header(array $headers, string $needle): ?string
    {
        foreach ($headers as $name => $value) {
            if (strcasecmp($name, $needle) === 0) {
                return $value;
            }
        }

        return null;
    }

    private function throwingHandler(string $message): Handler
    {
        return new class ($message) implements Handler {
            public function __construct(private readonly string $message)
            {
            }

            public function handle(Request $request): Response
            {
                throw new \RuntimeException($this->message);
            }
        };
    }
}
