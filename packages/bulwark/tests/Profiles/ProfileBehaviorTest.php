<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Tests\Profiles;

use Aegis\Bulwark\Profiles\CitadelProfile;
use Aegis\Bulwark\Profiles\GuardProfile;
use Aegis\Bulwark\Profiles\RampartProfile;
use Aegis\Bulwark\Tests\Support\FakeRequest;
use Aegis\Core\Http\BasicResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;
use Aegis\Core\Middleware\Handler;
use Aegis\Core\Middleware\PhalanxPipeline;
use PHPUnit\Framework\TestCase;

final class ProfileBehaviorTest extends TestCase
{
    public function testCitadelBlocksUnknownHost(): void
    {
        $app = (new PhalanxPipeline($this->terminal()))->pipe(
            (new CitadelProfile(['api.example.com']))->middlewares()
        );

        $response = $app->handle(new FakeRequest('GET', '/', ['Host' => 'evil.example.net']));

        $this->assertSame(400, $response->status());
        $this->assertSame('Invalid Host Header', $response->body());
        $this->assertSame('text/plain; charset=utf-8', $this->header($response->headers(), 'Content-Type'));
    }

    public function testRampartBlocksUnsupportedContentTypeOnPost(): void
    {
        $app = (new PhalanxPipeline($this->terminal()))->pipe(
            (new RampartProfile())->middlewares()
        );

        $response = $app->handle(new FakeRequest('POST', '/', ['Content-Type' => 'text/plain'], 'x'));

        $this->assertSame(415, $response->status());
        $this->assertSame('Unsupported Media Type', $response->body());
        $this->assertSame('text/plain; charset=utf-8', $this->header($response->headers(), 'Content-Type'));
    }

    public function testGuardAllowsSimpleGetRequest(): void
    {
        $app = (new PhalanxPipeline($this->terminal()))->pipe(
            (new GuardProfile())->middlewares()
        );

        $response = $app->handle(new FakeRequest('GET'));

        $this->assertSame(200, $response->status());
        $this->assertSame('ok', $response->body());
        $this->assertSame('nosniff', $this->header($response->headers(), 'X-Content-Type-Options'));
    }

    private function terminal(): Handler
    {
        return new class () implements Handler {
            public function handle(Request $request): Response
            {
                return (new BasicResponse(200))->write('ok');
            }
        };
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
