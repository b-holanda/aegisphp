<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Tests\Traffic;

use Aegis\Bulwark\Tests\Support\FakeRequest;
use Aegis\Bulwark\Traffic\ContentTypeShield;
use Aegis\Core\Http\BasicResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Middleware\CallableHandler;
use PHPUnit\Framework\TestCase;

final class ContentTypeShieldTest extends TestCase
{
    public function testAllowsConfiguredContentType(): void
    {
        $middleware = new ContentTypeShield(['application/json']);
        $request = new FakeRequest('POST', '/', ['Content-Type' => 'application/json; charset=utf-8']);

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => new BasicResponse(200))
        );

        $this->assertSame(200, $response->status());
    }

    public function testBlocksMissingContentTypeForEnforcedMethod(): void
    {
        $middleware = new ContentTypeShield(['application/json']);
        $request = new FakeRequest('POST', '/');

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => new BasicResponse(200))
        );

        $this->assertSame(415, $response->status());
        $this->assertSame('Unsupported Media Type', $response->body());
        $this->assertSame('text/plain; charset=utf-8', $this->header($response->headers(), 'Content-Type'));
    }

    public function testBlocksDisallowedContentType(): void
    {
        $middleware = new ContentTypeShield(['application/json']);
        $request = new FakeRequest('POST', '/', ['Content-Type' => 'text/xml']);

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => new BasicResponse(200))
        );

        $this->assertSame(415, $response->status());
        $this->assertSame('Unsupported Media Type', $response->body());
        $this->assertSame('text/plain; charset=utf-8', $this->header($response->headers(), 'Content-Type'));
    }

    public function testSkipsValidationForSafeMethod(): void
    {
        $middleware = new ContentTypeShield(['application/json']);
        $request = new FakeRequest('GET', '/');

        $response = $middleware->process(
            $request,
            new CallableHandler(static fn (Request $req): BasicResponse => new BasicResponse(200))
        );

        $this->assertSame(200, $response->status());
    }

    public function testRejectsBlankAllowedContentTypes(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new ContentTypeShield(['   ']);
    }

    public function testRejectsBlankEnforcedMethods(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new ContentTypeShield(['application/json'], ['   ']);
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
