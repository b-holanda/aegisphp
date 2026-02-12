<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Profiles;

use Aegis\Bulwark\Errors\CrashShieldMiddleware;
use Aegis\Bulwark\Headers\BulwarkHeaders;
use Aegis\Bulwark\Traffic\ContentTypeShield;
use Aegis\Bulwark\Traffic\MethodAllowlistShield;
use Aegis\Bulwark\Traffic\PayloadLimitShield;

final class RampartProfile implements SecurityProfile
{
    /**
     * @param list<string> $allowedMethods
     * @param list<string> $allowedContentTypes
     */
    public function __construct(
        private readonly array $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
        private readonly int $maxPayloadBytes = 1_048_576,
        private readonly array $allowedContentTypes = [
            'application/json',
            'application/x-www-form-urlencoded',
            'multipart/form-data',
        ]
    ) {
    }

    public function middlewares(): array
    {
        return [
            BulwarkHeaders::rampart(),
            new CrashShieldMiddleware(false),
            new MethodAllowlistShield($this->allowedMethods),
            new PayloadLimitShield($this->maxPayloadBytes),
            new ContentTypeShield($this->allowedContentTypes, ['POST', 'PUT', 'PATCH', 'DELETE']),
        ];
    }
}
