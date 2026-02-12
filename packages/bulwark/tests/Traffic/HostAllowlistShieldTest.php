<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Tests\Traffic;

use Aegis\Bulwark\Tests\Support\FakeRequest;
use Aegis\Bulwark\Traffic\HostAllowlistShield;
use Aegis\Core\Http\BasicResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Middleware\CallableHandler;
use PHPUnit\Framework\TestCase;

final class HostAllowlistShieldTest extends TestCase
{
    public function testAllowsRequestWhenHostIsInAllowlist(): void
    {
        $middleware = new HostAllowlistShield(['api.example.com']);
        $request = new FakeRequest('GET', '/', ['Host' => 'api.example.com:443']);

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => (new BasicResponse(200))->write('ok'))
        );

        $this->assertSame(200, $response->status());
    }

    public function testBlocksRequestWhenHostIsMissing(): void
    {
        $middleware = new HostAllowlistShield(['api.example.com']);
        $request = new FakeRequest('GET', '/');

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => new BasicResponse(200))
        );

        $this->assertSame(400, $response->status());
        $this->assertSame('Invalid Host Header', $response->body());
        $this->assertSame('text/plain; charset=utf-8', $this->header($response->headers(), 'Content-Type'));
    }

    public function testBlocksRequestWhenHostIsNotAllowed(): void
    {
        $middleware = new HostAllowlistShield(['api.example.com']);
        $request = new FakeRequest('GET', '/', ['Host' => 'evil.example.net']);

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => new BasicResponse(200))
        );

        $this->assertSame(400, $response->status());
        $this->assertSame('Invalid Host Header', $response->body());
        $this->assertSame('text/plain; charset=utf-8', $this->header($response->headers(), 'Content-Type'));
    }

    public function testRejectsEmptyAllowlist(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new HostAllowlistShield([]);
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
