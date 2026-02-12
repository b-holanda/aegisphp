<?php

declare(strict_types=1);

namespace Aegis\Core\Runtime;

use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;
use Aegis\Core\Middleware\Pipeline;

interface RuntimeStrategy
{
    public function run(Pipeline $app): void;
    public function toRequest(mixed $native): Request;
    public function emit(Response $response): void;
}
