<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Traffic;

use Aegis\Bulwark\Http\BulwarkResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;
use Aegis\Core\Middleware\Handler;
use Aegis\Core\Middleware\Middleware;

final class PayloadLimitShield implements Middleware
{
    public function __construct(private readonly int $maxBytes)
    {
        if ($maxBytes <= 0) {
            throw new \InvalidArgumentException('Payload limit must be greater than zero.');
        }
    }

    public function process(Request $request, Handler $next): Response
    {
        $contentLength = $request->header('content-length');
        if ($contentLength !== null) {
            $value = trim($contentLength);
            if ($value === '' || !ctype_digit($value)) {
                return $this->blocked(400, 'Invalid Content-Length');
            }

            if ((int) $value > $this->maxBytes) {
                return $this->blocked(413, 'Payload Too Large');
            }
        }

        if (\strlen($request->body()) > $this->maxBytes) {
            return $this->blocked(413, 'Payload Too Large');
        }

        return $next->handle($request);
    }

    private function blocked(int $status, string $message): Response
    {
        return (new BulwarkResponse($status))
            ->header('Content-Type', 'text/plain; charset=utf-8')
            ->write($message);
    }
}
