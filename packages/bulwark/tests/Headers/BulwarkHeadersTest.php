<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Tests\Headers;

use Aegis\Bulwark\Headers\BulwarkHeaders;
use Aegis\Bulwark\Tests\Support\FakeRequest;
use Aegis\Core\Http\BasicResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Middleware\CallableHandler;
use PHPUnit\Framework\TestCase;

final class BulwarkHeadersTest extends TestCase
{
    public function testAddsSecurityHeadersWhenMissing(): void
    {
        $middleware = BulwarkHeaders::guard();
        $request = new FakeRequest();

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => new BasicResponse(200))
        );

        $headers = $response->headers();
        $this->assertSame('nosniff', $this->header($headers, 'X-Content-Type-Options'));
        $this->assertSame('DENY', $this->header($headers, 'X-Frame-Options'));
        $this->assertSame('no-referrer', $this->header($headers, 'Referrer-Policy'));
    }

    public function testDoesNotOverwriteExistingHeadersByDefault(): void
    {
        $middleware = BulwarkHeaders::guard();
        $request = new FakeRequest();

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => (new BasicResponse(200))
                ->header('X-Frame-Options', 'SAMEORIGIN'))
        );

        $this->assertSame('SAMEORIGIN', $this->header($response->headers(), 'X-Frame-Options'));
    }

    public function testOverwriteModeReplacesHeader(): void
    {
        $middleware = new BulwarkHeaders(['X-Frame-Options' => 'DENY'], true);
        $request = new FakeRequest();

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => (new BasicResponse(200))
                ->header('X-Frame-Options', 'SAMEORIGIN'))
        );

        $this->assertSame('DENY', $this->header($response->headers(), 'X-Frame-Options'));
    }

    public function testMiddlewareReturnsNewResponseWithoutMutatingOriginalInstance(): void
    {
        $middleware = BulwarkHeaders::guard();
        $request = new FakeRequest();
        $base = new BasicResponse(200);

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => $base)
        );

        $this->assertNotSame($base, $response);
        $this->assertNull($this->header($base->headers(), 'X-Content-Type-Options'));
        $this->assertSame('nosniff', $this->header($response->headers(), 'X-Content-Type-Options'));
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
}
