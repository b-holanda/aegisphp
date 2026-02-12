<?php

declare(strict_types=1);

namespace Aegis\Core\Middleware;

use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;

final class CallableHandler implements Handler
{
    /** @var callable(Request):Response */
    private $fn;

    /** @param callable(Request):Response $fn */
    public function __construct(callable $fn)
    {
        $this->fn = $fn;
    }

    public function handle(Request $request): Response
    {
        return ($this->fn)($request);
    }
}
