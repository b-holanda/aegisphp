<?php

declare(strict_types=1);

namespace Aegis\Core\Tests\Config;

use Aegis\Core\Config\NativeEnv;
use PHPUnit\Framework\TestCase;

final class NativeEnvTest extends TestCase
{
    /** @var array<string,mixed> */
    private array $envBackup = [];

    /** @var array<string,mixed> */
    private array $serverBackup = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->envBackup = $_ENV;
        $this->serverBackup = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_ENV = $this->envBackup;
        $_SERVER = $this->serverBackup;
        parent::tearDown();
    }

    public function testInjectedValuesTakePrecedenceOverProcessEnvironment(): void
    {
        $key = 'AEGIS_CORE_ENV_PRECEDENCE';
        $previous = getenv($key);
        $_ENV[$key] = 'from-env-superglobal';
        $_SERVER[$key] = 'from-server-superglobal';

        putenv($key . '=from-process');

        try {
            $env = new NativeEnv([$key => 'from-constructor']);

            $this->assertSame('from-constructor', $env->get($key));
        } finally {
            $this->restoreEnv($key, $previous);
        }
    }

    public function testReadsFromEnvSuperglobalBeforeServerAndProcessEnvironment(): void
    {
        $key = 'AEGIS_CORE_ENV_SUPERGLOBAL';
        $previous = getenv($key);
        $_ENV[$key] = 'from-env-superglobal';
        $_SERVER[$key] = 'from-server-superglobal';
        putenv($key . '=from-process');

        try {
            $env = new NativeEnv();

            $this->assertSame('from-env-superglobal', $env->get($key));
        } finally {
            $this->restoreEnv($key, $previous);
        }
    }

    public function testFallsBackToServerSuperglobalBeforeProcessEnvironment(): void
    {
        $key = 'AEGIS_CORE_SERVER_FALLBACK';
        $previous = getenv($key);
        unset($_ENV[$key]);
        $_SERVER[$key] = 'from-server-superglobal';
        putenv($key . '=from-process');

        try {
            $env = new NativeEnv();

            $this->assertSame('from-server-superglobal', $env->get($key));
        } finally {
            $this->restoreEnv($key, $previous);
        }
    }

    public function testFallsBackToProcessEnvironment(): void
    {
        $key = 'AEGIS_CORE_ENV_FALLBACK';
        $previous = getenv($key);
        unset($_ENV[$key], $_SERVER[$key]);

        putenv($key . '=from-process');

        try {
            $env = new NativeEnv();

            $this->assertSame('from-process', $env->get($key));
        } finally {
            $this->restoreEnv($key, $previous);
        }
    }

    public function testRequireThrowsForMissingOrEmptyValues(): void
    {
        $env = new NativeEnv(['EMPTY_KEY' => '']);

        $this->expectException(\RuntimeException::class);

        $env->require('EMPTY_KEY');
    }

    private function restoreEnv(string $key, string|false $previous): void
    {
        if ($previous === false) {
            putenv($key);
            return;
        }

        putenv($key . '=' . $previous);
    }
}
