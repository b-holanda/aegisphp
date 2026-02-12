<?php

declare(strict_types=1);

namespace Aegis\Core\Tests\Http;

use Aegis\Core\Http\BasicResponse;
use PHPUnit\Framework\TestCase;

final class BasicResponseTest extends TestCase
{
    public function testDefaultsIncludeSafeContentTypeHeader(): void
    {
        $response = new BasicResponse();

        $this->assertSame('text/plain; charset=utf-8', $this->headerValue($response->headers(), 'Content-Type'));
    }

    public function testResponseMethodsAreImmutable(): void
    {
        $response = new BasicResponse();
        $next = $response
            ->withStatus(201)
            ->header('X-Trace-Id', 'abc123')
            ->write('ok');

        $this->assertSame(200, $response->status());
        $this->assertSame('', $response->body());
        $this->assertNull($this->headerValue($response->headers(), 'X-Trace-Id'));

        $this->assertSame(201, $next->status());
        $this->assertSame('ok', $next->body());
        $this->assertSame('abc123', $this->headerValue($next->headers(), 'X-Trace-Id'));
    }

    public function testInvalidStatusCodeIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new BasicResponse(99);
    }

    public function testHeaderReplacementIsCaseInsensitive(): void
    {
        $response = (new BasicResponse())->header('content-type', 'application/json');
        $headers = $response->headers();

        $this->assertSame('application/json', $this->headerValue($headers, 'Content-Type'));
        $this->assertSame(1, $this->countHeaders($headers, 'Content-Type'));
    }

    /**
     * @param array<string,string> $headers
     */
    private function headerValue(array $headers, string $needle): ?string
    {
        foreach ($headers as $name => $value) {
            if (strcasecmp($name, $needle) === 0) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @param array<string,string> $headers
     */
    private function countHeaders(array $headers, string $needle): int
    {
        $count = 0;

        foreach ($headers as $name => $_) {
            if (strcasecmp($name, $needle) === 0) {
                $count++;
            }
        }

        return $count;
    }
}
