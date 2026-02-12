<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Errors;

use Aegis\Bulwark\Http\BulwarkResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;
use Aegis\Core\Middleware\Handler;
use Aegis\Core\Middleware\Middleware;

final class CrashShieldMiddleware implements Middleware
{
    public function __construct(
        private readonly bool $debug = false,
        private readonly string $publicMessage = 'Internal Server Error'
    ) {
    }

    public function process(Request $request, Handler $next): Response
    {
        try {
            return $next->handle($request);
        } catch (\Throwable $e) {
            $message = $this->debug
                ? 'Internal Server Error: ' . $e->getMessage()
                : $this->publicMessage;

            return (new BulwarkResponse(500))
                ->header('Content-Type', 'text/plain; charset=utf-8')
                ->write($message);
        }
    }
}
