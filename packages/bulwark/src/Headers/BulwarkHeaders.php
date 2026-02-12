<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Headers;

use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;
use Aegis\Core\Middleware\Handler;
use Aegis\Core\Middleware\Middleware;

final class BulwarkHeaders implements Middleware
{
    /** @var array<string,string> */
    private const BASE_HEADERS = [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'DENY',
        'Referrer-Policy' => 'no-referrer',
        'X-Permitted-Cross-Domain-Policies' => 'none',
    ];

    /** @var array<string,string> */
    private const RAMPART_HEADERS = [
        'Cross-Origin-Opener-Policy' => 'same-origin',
        'Cross-Origin-Resource-Policy' => 'same-origin',
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
    ];

    /** @var array<string,string> */
    private const CITADEL_HEADERS = [
        'Content-Security-Policy' => "default-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'none'",
        'X-DNS-Prefetch-Control' => 'off',
        'X-Download-Options' => 'noopen',
    ];

    /** @var array<string,string> */
    private readonly array $headers;

    /**
     * @param array<string,string> $headers
     */
    public function __construct(array $headers = [], private readonly bool $overwrite = false)
    {
        $this->headers = $headers === [] ? self::BASE_HEADERS : $headers;
    }

    public static function guard(): self
    {
        return new self(self::BASE_HEADERS);
    }

    public static function rampart(): self
    {
        return new self(array_replace(self::BASE_HEADERS, self::RAMPART_HEADERS));
    }

    public static function citadel(): self
    {
        return new self(array_replace(self::BASE_HEADERS, self::RAMPART_HEADERS, self::CITADEL_HEADERS));
    }

    public function process(Request $request, Handler $next): Response
    {
        $response = $next->handle($request);

        foreach ($this->headers as $name => $value) {
            if (!$this->overwrite && $this->hasHeader($response->headers(), $name)) {
                continue;
            }

            $response = $response->header($name, $value);
        }

        return $response;
    }

    /**
     * @param array<string,string> $headers
     */
    private function hasHeader(array $headers, string $name): bool
    {
        foreach ($headers as $headerName => $_) {
            if (strcasecmp($headerName, $name) === 0) {
                return true;
            }
        }

        return false;
    }
}
