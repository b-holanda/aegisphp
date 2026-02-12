<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Profiles;

use Aegis\Bulwark\Errors\CrashShieldMiddleware;
use Aegis\Bulwark\Headers\BulwarkHeaders;
use Aegis\Bulwark\Traffic\ContentTypeShield;
use Aegis\Bulwark\Traffic\HostAllowlistShield;
use Aegis\Bulwark\Traffic\MethodAllowlistShield;
use Aegis\Bulwark\Traffic\PayloadLimitShield;

final class CitadelProfile implements SecurityProfile
{
    /**
     * @param list<string> $allowedHosts
     * @param list<string> $allowedMethods
     * @param list<string> $allowedContentTypes
     */
    public function __construct(
        private readonly array $allowedHosts,
        private readonly array $allowedMethods = ['GET', 'POST'],
        private readonly int $maxPayloadBytes = 262_144,
        private readonly array $allowedContentTypes = ['application/json']
    ) {
        if ($allowedHosts === []) {
            throw new \InvalidArgumentException('CitadelProfile requires at least one allowed host.');
        }
    }

    public function middlewares(): array
    {
        return [
            BulwarkHeaders::citadel(),
            new CrashShieldMiddleware(false),
            new HostAllowlistShield($this->allowedHosts),
            new MethodAllowlistShield($this->allowedMethods),
            new PayloadLimitShield($this->maxPayloadBytes),
            new ContentTypeShield($this->allowedContentTypes, ['POST']),
        ];
    }
}
