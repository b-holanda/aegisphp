<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Traffic;

use Aegis\Bulwark\Http\BulwarkResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;
use Aegis\Core\Middleware\Handler;
use Aegis\Core\Middleware\Middleware;

final class MethodAllowlistShield implements Middleware
{
    /** @var list<string> */
    private readonly array $allowedMethods;

    /**
     * @param list<string> $allowedMethods
     */
    public function __construct(array $allowedMethods)
    {
        $normalized = array_values(array_filter(array_unique(array_map(
            static fn (string $method): string => strtoupper(trim($method)),
            $allowedMethods
        )), static fn (string $method): bool => $method !== ''));

        if ($normalized === []) {
            throw new \InvalidArgumentException('Method allowlist cannot be empty.');
        }

        $this->allowedMethods = $normalized;
    }

    public function process(Request $request, Handler $next): Response
    {
        $method = strtoupper($request->method());

        if (\in_array($method, $this->allowedMethods, true)) {
            return $next->handle($request);
        }

        return (new BulwarkResponse(405))
            ->header('Content-Type', 'text/plain; charset=utf-8')
            ->header('Allow', implode(', ', $this->allowedMethods))
            ->write('Method Not Allowed');
    }
}
