<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Traffic;

use Aegis\Bulwark\Http\BulwarkResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;
use Aegis\Core\Middleware\Handler;
use Aegis\Core\Middleware\Middleware;

final class ContentTypeShield implements Middleware
{
    /** @var list<string> */
    private readonly array $allowedContentTypes;

    /** @var list<string> */
    private readonly array $enforcedMethods;

    /**
     * @param list<string> $allowedContentTypes
     * @param list<string> $enforcedMethods
     */
    public function __construct(array $allowedContentTypes, array $enforcedMethods = ['POST', 'PUT', 'PATCH', 'DELETE'])
    {
        $types = array_values(array_filter(array_unique(array_map(
            static fn (string $type): string => strtolower(trim($type)),
            $allowedContentTypes
        )), static fn (string $type): bool => $type !== ''));

        if ($types === []) {
            throw new \InvalidArgumentException('Allowed content types cannot be empty.');
        }

        $methods = array_values(array_filter(array_unique(array_map(
            static fn (string $method): string => strtoupper(trim($method)),
            $enforcedMethods
        )), static fn (string $method): bool => $method !== ''));

        if ($methods === []) {
            throw new \InvalidArgumentException('Enforced methods cannot be empty.');
        }

        $this->allowedContentTypes = $types;
        $this->enforcedMethods = $methods;
    }

    public function process(Request $request, Handler $next): Response
    {
        $method = strtoupper($request->method());
        if (!\in_array($method, $this->enforcedMethods, true)) {
            return $next->handle($request);
        }

        $contentType = $request->header('content-type');
        if ($contentType === null || trim($contentType) === '') {
            return $this->blocked();
        }

        $mediaType = strtolower(trim(explode(';', $contentType, 2)[0]));
        if (!\in_array($mediaType, $this->allowedContentTypes, true)) {
            return $this->blocked();
        }

        return $next->handle($request);
    }

    private function blocked(): Response
    {
        return (new BulwarkResponse(415))
            ->header('Content-Type', 'text/plain; charset=utf-8')
            ->write('Unsupported Media Type');
    }
}
