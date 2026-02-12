<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Tests\Http;

use Aegis\Bulwark\Http\BulwarkResponse;
use PHPUnit\Framework\TestCase;

final class BulwarkResponseTest extends TestCase
{
    public function testResponseIsImmutable(): void
    {
        $response = new BulwarkResponse(200, ['X-Test' => 'one'], 'a');
        $next = $response
            ->withStatus(201)
            ->header('X-Test', 'two')
            ->write('b');

        $this->assertSame(200, $response->status());
        $this->assertSame('a', $response->body());
        $this->assertSame('one', $this->header($response->headers(), 'X-Test'));

        $this->assertSame(201, $next->status());
        $this->assertSame('ab', $next->body());
        $this->assertSame('two', $this->header($next->headers(), 'X-Test'));
    }

    public function testRejectsInvalidStatusCode(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new BulwarkResponse(99);
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
