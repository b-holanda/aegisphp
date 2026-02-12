<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Tests\Traffic;

use Aegis\Bulwark\Tests\Support\FakeRequest;
use Aegis\Bulwark\Traffic\PayloadLimitShield;
use Aegis\Core\Http\BasicResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Middleware\CallableHandler;
use PHPUnit\Framework\TestCase;

final class PayloadLimitShieldTest extends TestCase
{
    public function testAllowsRequestWithinLimit(): void
    {
        $middleware = new PayloadLimitShield(8);
        $request = new FakeRequest('POST', '/', ['Content-Length' => '4'], 'test');

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => new BasicResponse(200))
        );

        $this->assertSame(200, $response->status());
    }

    public function testBlocksRequestWhenContentLengthExceedsLimit(): void
    {
        $middleware = new PayloadLimitShield(8);
        $request = new FakeRequest('POST', '/', ['Content-Length' => '20']);

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => new BasicResponse(200))
        );

        $this->assertSame(413, $response->status());
        $this->assertSame('Payload Too Large', $response->body());
        $this->assertSame('text/plain; charset=utf-8', $this->header($response->headers(), 'Content-Type'));
    }

    public function testBlocksRequestWhenBodyExceedsLimit(): void
    {
        $middleware = new PayloadLimitShield(8);
        $request = new FakeRequest('POST', '/', [], '0123456789');

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => new BasicResponse(200))
        );

        $this->assertSame(413, $response->status());
        $this->assertSame('Payload Too Large', $response->body());
        $this->assertSame('text/plain; charset=utf-8', $this->header($response->headers(), 'Content-Type'));
    }

    public function testBlocksInvalidContentLength(): void
    {
        $middleware = new PayloadLimitShield(8);
        $request = new FakeRequest('POST', '/', ['Content-Length' => 'abc']);

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => new BasicResponse(200))
        );

        $this->assertSame(400, $response->status());
        $this->assertSame('Invalid Content-Length', $response->body());
        $this->assertSame('text/plain; charset=utf-8', $this->header($response->headers(), 'Content-Type'));
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
