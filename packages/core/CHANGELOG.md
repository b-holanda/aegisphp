# Changelog

## [0.1.0] - 2026-02-12

M0 baseline do `aegisphp/core`.

### Added

- Contratos HTTP: `Request`, `Response`.
- Implementacoes HTTP imutaveis: `BasicRequest`, `BasicResponse`.
- Contratos de middleware: `Middleware`, `Handler`, `Pipeline`.
- Implementacoes de middleware: `PhalanxPipeline`, `CallableHandler`.
- Contratos de config/env: `Config`, `Env`, `ConfigLoader`.
- Implementacoes de config/env: `ArrayConfig`, `NativeEnv`, `PhpArrayConfigLoader`.
- Contratos de eventos: `Event`, `Listener`, `EventBus`.
- Implementacao de eventos: `SimpleEventBus`.
- Contrato de runtime: `RuntimeStrategy`.
- Implementacao de runtime para PHP-FPM: `PhpFpmRuntime`.
- Suite de testes smoke/unit para HTTP, middleware, config, events e runtime.

### Security/Behavior Defaults

- Fail-closed no pipeline default (`404 Not Found`).
- `Content-Type: text/plain; charset=utf-8` por padrao no response/emit.
- Lookup de headers case-insensitive.
- Precedencia de env: valores injetados -> `$_ENV` -> `$_SERVER` -> `getenv()`.
