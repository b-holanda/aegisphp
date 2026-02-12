<?php

declare(strict_types=1);

namespace Aegis\Core\Tests\Http;

use Aegis\Core\Http\BasicRequest;
use PHPUnit\Framework\TestCase;

final class BasicRequestTest extends TestCase
{
    public function testWithAttributeReturnsNewInstanceWithoutMutatingOriginal(): void
    {
        $request = new BasicRequest('GET', '/health');
        $next = $request->withAttribute('user_id', 42);

        $this->assertNotSame($request, $next);
        $this->assertNull($request->attribute('user_id'));
        $this->assertSame(42, $next->attribute('user_id'));
    }

    public function testHeaderLookupIsCaseInsensitive(): void
    {
        $request = new BasicRequest('POST', '/users', ['Content-Type' => 'application/json']);

        $this->assertSame('application/json', $request->header('content-type'));
        $this->assertSame('application/json', $request->header('CONTENT-TYPE'));
    }
}
