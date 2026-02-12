<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Tests\Traffic;

use Aegis\Bulwark\Tests\Support\FakeRequest;
use Aegis\Bulwark\Traffic\MethodAllowlistShield;
use Aegis\Core\Http\BasicResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Middleware\CallableHandler;
use PHPUnit\Framework\TestCase;

final class MethodAllowlistShieldTest extends TestCase
{
    public function testAllowsConfiguredMethod(): void
    {
        $middleware = new MethodAllowlistShield(['GET', 'POST']);
        $request = new FakeRequest('GET');

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => (new BasicResponse(200))->write('ok'))
        );

        $this->assertSame(200, $response->status());
        $this->assertSame('ok', $response->body());
    }

    public function testBlocksDisallowedMethodWithAllowHeader(): void
    {
        $middleware = new MethodAllowlistShield(['GET', 'POST']);
        $request = new FakeRequest('TRACE');

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => (new BasicResponse(200))->write('ok'))
        );

        $this->assertSame(405, $response->status());
        $this->assertSame('Method Not Allowed', $response->body());
        $this->assertSame('GET, POST', $this->header($response->headers(), 'Allow'));
        $this->assertSame('text/plain; charset=utf-8', $this->header($response->headers(), 'Content-Type'));
    }

    public function testRejectsEmptyAllowlist(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new MethodAllowlistShield([]);
    }

    public function testRejectsBlankMethodEntries(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new MethodAllowlistShield(['   ']);
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
