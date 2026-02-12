<?php

declare(strict_types=1);

namespace Aegis\Core\Middleware;

use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;

interface Handler
{
    public function handle(Request $request): Response;
}
