# aegisphp/core

Core mínimo do AegisPHP: abstrações HTTP, pipeline de middleware, config/env,
event bus simples e estratégia de runtime.

Escopo do milestone M0:
- HTTP abstractions
- Middleware pipeline
- Config + Env
- Event bus simples
- Runtime strategy (`PhpFpmRuntime`)

## Instalação

```bash
composer require aegisphp/core
```

## Exemplo mínimo funcional

```php
<?php
declare(strict_types=1);

use Aegis\Core\Http\BasicResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;
use Aegis\Core\Middleware\Handler;
use Aegis\Core\Middleware\Middleware;
use Aegis\Core\Middleware\PhalanxPipeline;
use Aegis\Core\Runtime\PhpFpmRuntime;

$app = (new PhalanxPipeline())->pipe([
    new class implements Middleware {
        public function process(Request $request, Handler $next): Response
        {
            return (new BasicResponse(200))->write('Hello from Aegis core');
        }
    },
]);

(new PhpFpmRuntime())->run($app);
```

## Interfaces públicas

| Área | Interface |
|------|-----------|
| HTTP | `Aegis\Core\Http\Request` |
| HTTP | `Aegis\Core\Http\Response` |
| Middleware | `Aegis\Core\Middleware\Handler` |
| Middleware | `Aegis\Core\Middleware\Middleware` |
| Middleware | `Aegis\Core\Middleware\Pipeline` |
| Config | `Aegis\Core\Config\Config` |
| Config | `Aegis\Core\Config\Env` |
| Config | `Aegis\Core\Config\ConfigLoader` |
| Events | `Aegis\Core\Events\Event` |
| Events | `Aegis\Core\Events\Listener` |
| Events | `Aegis\Core\Events\EventBus` |
| Runtime | `Aegis\Core\Runtime\RuntimeStrategy` |

## Tabela de dependências

| Dependência | Tipo | Obrigatória |
|-------------|------|-------------|
| `php:^8.5` | runtime | sim |
| `phpunit/phpunit:^11.0` | desenvolvimento | não |

## Notas de segurança

- O pipeline padrão fecha em `404 Not Found` (fail closed).
- `Content-Type` é definido por padrão como `text/plain; charset=utf-8`.
- `Request` e `Response` são imutáveis via métodos `with*`/`write`.

## Invariantes de comportamento

- `BasicRequest::header()` faz lookup case-insensitive.
- `PhalanxPipeline::pipe()` substitui a stack anterior (não acumula chamadas anteriores).
- `NativeEnv` resolve com precedência: valores injetados -> `$_ENV` -> `$_SERVER` -> `getenv()`.
- `PhpFpmRuntime::emit()` aplica `Content-Type: text/plain; charset=utf-8` quando ausente.
