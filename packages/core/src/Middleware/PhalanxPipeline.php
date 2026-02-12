<?php

declare(strict_types=1);

namespace Aegis\Core\Middleware;

use Aegis\Core\Http\BasicResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;

final class PhalanxPipeline implements Pipeline
{
    /** @var list<Middleware> */
    private array $stack = [];

    private Handler $terminal;

    public function __construct(?Handler $terminal = null)
    {
        $this->terminal = $terminal ?? new CallableHandler(
            fn (Request $r): Response => (new BasicResponse(404))
                ->header('Content-Type', 'text/plain; charset=utf-8')
                ->write('Not Found')
        );
    }

    public function pipe(array $middlewares): Pipeline
    {
        foreach ($middlewares as $m) {
            if (!$m instanceof Middleware) {
                throw new \InvalidArgumentException('All items passed to pipe() must implement Middleware.');
            }
        }

        $this->stack = array_values($middlewares);

        return $this;
    }

    public function handle(Request $request): Response
    {
        $handler = $this->terminal;

        for ($i = \count($this->stack) - 1; $i >= 0; $i--) {
            $middleware = $this->stack[$i];
            $next = $handler;

            $handler = new class ($middleware, $next) implements Handler {
                public function __construct(private Middleware $m, private Handler $next)
                {
                }
                public function handle(Request $request): Response
                {
                    return $this->m->process($request, $this->next);
                }
            };
        }

        return $handler->handle($request);
    }
}
