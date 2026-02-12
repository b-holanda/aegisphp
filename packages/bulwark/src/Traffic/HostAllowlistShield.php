<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Traffic;

use Aegis\Bulwark\Http\BulwarkResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;
use Aegis\Core\Middleware\Handler;
use Aegis\Core\Middleware\Middleware;

final class HostAllowlistShield implements Middleware
{
    /** @var list<string> */
    private readonly array $allowedHosts;

    /**
     * @param list<string> $allowedHosts
     */
    public function __construct(array $allowedHosts)
    {
        $normalized = [];
        foreach ($allowedHosts as $host) {
            $value = $this->normalizeHost($host);
            if ($value !== '') {
                $normalized[] = $value;
            }
        }

        $normalized = array_values(array_unique($normalized));
        if ($normalized === []) {
            throw new \InvalidArgumentException('Host allowlist cannot be empty.');
        }

        $this->allowedHosts = $normalized;
    }

    public function process(Request $request, Handler $next): Response
    {
        $hostHeader = $request->header('host');
        if ($hostHeader === null || trim($hostHeader) === '') {
            return $this->blocked(400, 'Invalid Host Header');
        }

        $host = $this->normalizeHost($hostHeader);
        if (!\in_array($host, $this->allowedHosts, true)) {
            return $this->blocked(400, 'Invalid Host Header');
        }

        return $next->handle($request);
    }

    private function blocked(int $status, string $message): Response
    {
        return (new BulwarkResponse($status))
            ->header('Content-Type', 'text/plain; charset=utf-8')
            ->write($message);
    }

    private function normalizeHost(string $host): string
    {
        $value = trim(strtolower(explode(',', $host, 2)[0]));
        $value = rtrim($value, '.');

        if ($value === '') {
            return '';
        }

        if (str_starts_with($value, '[')) {
            $end = strpos($value, ']');
            if ($end !== false) {
                return substr($value, 1, $end - 1);
            }

            return trim($value, '[]');
        }

        if (substr_count($value, ':') === 1 && !filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            return explode(':', $value, 2)[0];
        }

        return $value;
    }
}
