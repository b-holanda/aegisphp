<?php

declare(strict_types=1);

namespace Aegis\Core\Tests\Config;

use Aegis\Core\Config\PhpArrayConfigLoader;
use PHPUnit\Framework\TestCase;

final class PhpArrayConfigLoaderTest extends TestCase
{
    public function testLoadsAndMergesArraySources(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'aegis_cfg_');
        if ($file === false) {
            $this->fail('Could not create temporary config file.');
        }

        file_put_contents(
            $file,
            "<?php return ['db' => ['host' => 'localhost'], 'cache' => ['ttl' => 30]];"
        );

        try {
            $loader = new PhpArrayConfigLoader();
            $config = $loader->load([
                ['db' => ['host' => '127.0.0.1', 'port' => 3306]],
                $file,
            ]);

            $this->assertSame('localhost', $config->get('db.host'));
            $this->assertSame(3306, $config->get('db.port'));
            $this->assertSame(30, $config->get('cache.ttl'));
        } finally {
            @unlink($file);
        }
    }

    public function testThrowsWhenSourceTypeIsUnsupported(): void
    {
        $loader = new PhpArrayConfigLoader();

        $this->expectException(\InvalidArgumentException::class);

        $loader->load([new \stdClass()]);
    }

    public function testThrowsWhenFileDoesNotReturnArray(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'aegis_cfg_');
        if ($file === false) {
            $this->fail('Could not create temporary config file.');
        }

        file_put_contents($file, "<?php return 'invalid';");

        try {
            $loader = new PhpArrayConfigLoader();

            $this->expectException(\RuntimeException::class);

            $loader->load([$file]);
        } finally {
            @unlink($file);
        }
    }
}
