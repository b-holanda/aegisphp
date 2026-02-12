<?php

declare(strict_types=1);

namespace Aegis\Core\Tests\Config;

use Aegis\Core\Config\ArrayConfig;
use PHPUnit\Framework\TestCase;

final class ArrayConfigTest extends TestCase
{
    public function testReadsDotNotationKeys(): void
    {
        $config = new ArrayConfig([
            'db' => [
                'host' => 'localhost',
                'port' => 3306,
            ],
        ]);

        $this->assertSame('localhost', $config->get('db.host'));
        $this->assertSame(3306, $config->get('db.port'));
    }

    public function testReturnsDefaultWhenKeyIsMissing(): void
    {
        $config = new ArrayConfig(['app' => ['name' => 'aegis']]);

        $this->assertSame('fallback', $config->get('app.version', 'fallback'));
    }
}
