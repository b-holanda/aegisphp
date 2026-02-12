<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Profiles;

use Aegis\Bulwark\Errors\CrashShieldMiddleware;
use Aegis\Bulwark\Headers\BulwarkHeaders;
use Aegis\Bulwark\Traffic\MethodAllowlistShield;
use Aegis\Bulwark\Traffic\PayloadLimitShield;

final class GuardProfile implements SecurityProfile
{
    /**
     * @param list<string> $allowedMethods
     */
    public function __construct(
        private readonly array $allowedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
        private readonly int $maxPayloadBytes = 2_097_152
    ) {
    }

    public function middlewares(): array
    {
        return [
            BulwarkHeaders::guard(),
            new CrashShieldMiddleware(false),
            new MethodAllowlistShield($this->allowedMethods),
            new PayloadLimitShield($this->maxPayloadBytes),
        ];
    }
}
