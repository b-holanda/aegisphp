<?php

declare(strict_types=1);

namespace Aegis\Core\Tests\Smoke;

use Aegis\Core\Http\BasicResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Middleware\Handler;
use Aegis\Core\Middleware\PhalanxPipeline;
use PHPUnit\Framework\TestCase;

final class PhalanxPipelineTest extends TestCase
{
    public function test_pipeline_executes_middlewares_in_order(): void
    {
        $trace = new \stdClass();
        $trace->entries = [];

        $m1 = new class ($trace) implements \Aegis\Core\Middleware\Middleware {
            public function __construct(private \stdClass $trace)
            {
            }
            public function process(Request $request, Handler $next): \Aegis\Core\Http\Response
            {
                $this->trace->entries[] = 'm1:in';
                $res = $next->handle($request);
                $this->trace->entries[] = 'm1:out';
                return $res;
            }
        };

        $m2 = new class ($trace) implements \Aegis\Core\Middleware\Middleware {
            public function __construct(private \stdClass $trace)
            {
            }
            public function process(Request $request, Handler $next): \Aegis\Core\Http\Response
            {
                $this->trace->entries[] = 'm2:in';
                $res = $next->handle($request);
                $this->trace->entries[] = 'm2:out';
                return $res;
            }
        };

        $app = (new PhalanxPipeline(new class () implements Handler {
            public function handle(Request $request): \Aegis\Core\Http\Response
            {
                return (new BasicResponse(200))->write('ok');
            }
        }))->pipe([$m1, $m2]);

        $res = $app->handle(new DummyRequest());

        $this->assertSame(200, $res->status());
        $this->assertSame('ok', $res->body());
        $this->assertSame(['m1:in', 'm2:in', 'm2:out', 'm1:out'], $trace->entries);
    }

    public function test_pipeline_defaults_to_fail_closed_when_no_terminal_is_provided(): void
    {
        $response = (new PhalanxPipeline())->handle(new DummyRequest());

        $this->assertSame(404, $response->status());
        $this->assertSame('Not Found', $response->body());
        $this->assertSame('text/plain; charset=utf-8', $this->headerValue($response->headers(), 'Content-Type'));
    }

    public function test_pipe_replaces_previous_stack_instead_of_accumulating(): void
    {
        $trace = new \stdClass();
        $trace->entries = [];

        $m1 = new class ($trace) implements \Aegis\Core\Middleware\Middleware {
            public function __construct(private \stdClass $trace)
            {
            }

            public function process(Request $request, Handler $next): \Aegis\Core\Http\Response
            {
                $this->trace->entries[] = 'm1';

                return $next->handle($request);
            }
        };

        $m2 = new class ($trace) implements \Aegis\Core\Middleware\Middleware {
            public function __construct(private \stdClass $trace)
            {
            }

            public function process(Request $request, Handler $next): \Aegis\Core\Http\Response
            {
                $this->trace->entries[] = 'm2';

                return $next->handle($request);
            }
        };

        $pipeline = new PhalanxPipeline(new class () implements Handler {
            public function handle(Request $request): \Aegis\Core\Http\Response
            {
                return (new BasicResponse(200))->write('ok');
            }
        });

        $pipeline->pipe([$m1]);
        $pipeline->pipe([$m2]);
        $pipeline->handle(new DummyRequest());

        $this->assertSame(['m2'], $trace->entries);
    }

    /**
     * @param array<string,string> $headers
     */
    private function headerValue(array $headers, string $needle): ?string
    {
        foreach ($headers as $name => $value) {
            if (strcasecmp($name, $needle) === 0) {
                return $value;
            }
        }

        return null;
    }
}
