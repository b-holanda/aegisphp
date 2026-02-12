# aegisphp/bulwark

Middlewares de segurança para serviços públicos com três níveis de inicialização:

1. `CitadelProfile` (totalmente blindado)
2. `RampartProfile` (restritivo por padrão)
3. `GuardProfile` (leve e básico)

## Instalação

```bash
composer require aegisphp/bulwark
```

## Exemplo 1: totalmente blindado (`CitadelProfile`)

```php
<?php
declare(strict_types=1);

use Aegis\Bulwark\Profiles\CitadelProfile;
use Aegis\Core\Http\BasicResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;
use Aegis\Core\Middleware\Handler;
use Aegis\Core\Middleware\Middleware;
use Aegis\Core\Middleware\PhalanxPipeline;

$app = (new PhalanxPipeline(new class implements Handler {
    public function handle(Request $request): Response
    {
        return (new BasicResponse(200))->write('ok');
    }
}))->pipe([
    ...(new CitadelProfile(['api.exemplo.com']))->middlewares(),
    new class implements Middleware {
        public function process(Request $request, Handler $next): Response
        {
            return $next->handle($request);
        }
    },
]);
```

## Exemplo 2: restritivo por padrão (`RampartProfile`)

```php
<?php
declare(strict_types=1);

use Aegis\Bulwark\Profiles\RampartProfile;
use Aegis\Core\Http\BasicResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;
use Aegis\Core\Middleware\Handler;
use Aegis\Core\Middleware\Middleware;
use Aegis\Core\Middleware\PhalanxPipeline;

$app = (new PhalanxPipeline(new class implements Handler {
    public function handle(Request $request): Response
    {
        return (new BasicResponse(200))->write('ok');
    }
}))->pipe([
    ...(new RampartProfile())->middlewares(),
    new class implements Middleware {
        public function process(Request $request, Handler $next): Response
        {
            return $next->handle($request);
        }
    },
]);
```

## Exemplo 3: leve e básico (`GuardProfile`)

```php
<?php
declare(strict_types=1);

use Aegis\Bulwark\Profiles\GuardProfile;
use Aegis\Core\Http\BasicResponse;
use Aegis\Core\Http\Request;
use Aegis\Core\Http\Response;
use Aegis\Core\Middleware\Handler;
use Aegis\Core\Middleware\Middleware;
use Aegis\Core\Middleware\PhalanxPipeline;

$app = (new PhalanxPipeline(new class implements Handler {
    public function handle(Request $request): Response
    {
        return (new BasicResponse(200))->write('ok');
    }
}))->pipe([
    ...(new GuardProfile())->middlewares(),
    new class implements Middleware {
        public function process(Request $request, Handler $next): Response
        {
            return $next->handle($request);
        }
    },
]);
```

## Interfaces públicas

| Área | Interface |
|------|-----------|
| Profiles | `Aegis\Bulwark\Profiles\SecurityProfile` |

## Tabela de dependências

| Dependência | Tipo | Obrigatória |
|-------------|------|-------------|
| `php:^8.5` | runtime | sim |
| `aegisphp/core:*` | runtime | sim |
| `phpunit/phpunit:^11.0` | desenvolvimento | não |
