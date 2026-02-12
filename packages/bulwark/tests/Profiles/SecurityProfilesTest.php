<?php

declare(strict_types=1);

namespace Aegis\Bulwark\Tests\Profiles;

use Aegis\Bulwark\Errors\CrashShieldMiddleware;
use Aegis\Bulwark\Headers\BulwarkHeaders;
use Aegis\Bulwark\Profiles\CitadelProfile;
use Aegis\Bulwark\Profiles\GuardProfile;
use Aegis\Bulwark\Profiles\RampartProfile;
use Aegis\Bulwark\Traffic\ContentTypeShield;
use Aegis\Bulwark\Traffic\HostAllowlistShield;
use Aegis\Bulwark\Traffic\MethodAllowlistShield;
use Aegis\Bulwark\Traffic\PayloadLimitShield;
use PHPUnit\Framework\TestCase;

final class SecurityProfilesTest extends TestCase
{
    public function testGuardProfileComposition(): void
    {
        $profile = new GuardProfile();

        $this->assertSame([
            BulwarkHeaders::class,
            CrashShieldMiddleware::class,
            MethodAllowlistShield::class,
            PayloadLimitShield::class,
        ], $this->classes($profile->middlewares()));
    }

    public function testRampartProfileComposition(): void
    {
        $profile = new RampartProfile();

        $this->assertSame([
            BulwarkHeaders::class,
            CrashShieldMiddleware::class,
            MethodAllowlistShield::class,
            PayloadLimitShield::class,
            ContentTypeShield::class,
        ], $this->classes($profile->middlewares()));
    }

    public function testCitadelProfileComposition(): void
    {
        $profile = new CitadelProfile(['api.example.com']);

        $this->assertSame([
            BulwarkHeaders::class,
            CrashShieldMiddleware::class,
            HostAllowlistShield::class,
            MethodAllowlistShield::class,
            PayloadLimitShield::class,
            ContentTypeShield::class,
        ], $this->classes($profile->middlewares()));
    }

    public function testCitadelRequiresHostAllowlist(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CitadelProfile([]);
    }

    /**
     * @param list<object> $middlewares
     * @return list<class-string>
     */
    private function classes(array $middlewares): array
    {
        return array_map(
            static fn (object $middleware): string => $middleware::class,
            $middlewares
        );
    }
}
