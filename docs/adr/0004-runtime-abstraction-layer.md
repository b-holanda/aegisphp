# 🛡️ ADR-0004 — Runtime Abstraction Layer (php-fpm, RoadRunner, Franklin)

- **Status:** Accepted
- **Date:** 2026-02-12
- **Authors:** AegisPHP Core Maintainers
- **Related:** ADR-0001, ADR-0002, ADR-0003, MANIFEST.md

---

## 📌 Context

PHP applications can run under multiple execution environments (“runtimes”):

- **php-fpm** (classic request-per-process model)
- **RoadRunner** (long-lived worker model)
- **Franklin** (runtime/worker-based execution)
- Potentially others (CLI, Swoole, etc.)

Frameworks often tie themselves to a single runtime model, which creates:
- Lock-in
- Hidden assumptions about memory and lifecycle
- Inconsistent behavior between environments
- Difficulty integrating into enterprise infrastructure

AegisPHP aims to remain predictable and modular across runtimes,
while keeping the core minimal and free of runtime-specific coupling.

---

## 🎯 Decision

AegisPHP introduces a **Runtime Abstraction Layer** using the Strategy Pattern.

1. The core defines a **RuntimeStrategy** interface responsible for:
   - Converting native runtime input into an Aegis Request
   - Executing the application pipeline
   - Emitting the Aegis Response back to the runtime

2. Each runtime implementation is an adapter that lives in:
   - `core` (only for php-fpm reference implementation) OR
   - a dedicated module (e.g., `watchtower` or future `bastion-runtime-*` packages)

3. Application code composes the runtime explicitly:
   - No runtime auto-detection in core
   - No hidden execution flow

---

## 🛡️ Security Considerations

Different runtimes introduce different security and safety concerns:

- Long-lived workers (RoadRunner/Franklin) risk:
  - state leakage between requests
  - memory growth
  - accidental caching of sensitive data
  - reuse of mutable objects

This ADR mandates:

- Explicit runtime selection (no hidden detection)
- Immutable core request/response abstractions
- Clear lifecycle boundaries
- Avoiding global state

Security-first design must remain consistent regardless of runtime.

---

## 🧱 Architectural Impact

- The core defines an explicit runtime boundary.
- Runtime-specific concerns do not leak into core contracts.
- The middleware pipeline remains runtime-agnostic.
- Enables enterprise adoption where runtime choice is infrastructure-driven.

This supports modular independence and avoids framework lock-in.

---

## ⚖️ Trade-offs

### Advantages

- Predictable behavior across runtimes
- No hidden runtime assumptions
- Easier auditing and debugging
- Supports both classic and worker-based deployments
- Clear separation of concerns

### Disadvantages

- Slightly more configuration (explicit strategy selection)
- Requires runtime adapters to be maintained and tested
- Users must understand lifecycle differences when using long-lived workers

---

## 🚀 Implementation Notes

### RuntimeStrategy Contract (conceptual)

A RuntimeStrategy should provide:
- `toRequest(native): Request`
- `run(Pipeline $app): void`
- `emit(Response $response): void`

### Baseline Implementation

- `PhpFpmRuntime` is provided as a reference adapter in core.
- RoadRunner and Franklin adapters should be separate packages once stabilized.

### Testing

- Each runtime adapter must have integration tests.
- For long-lived worker runtimes:
  - tests must assert no state leakage across requests
  - memory usage should be monitored in benchmarks

Versioning impact:
- Core contract changes to RuntimeStrategy are MAJOR changes and must be rare.

---

## 📚 References

- ADR-0001 — Security-First as a Foundational Constraint
- ADR-0002 — Zero-Magic Policy
- ADR-0003 — Core Minimalism & Modular Independence
- Strategy Pattern (GoF)
- OWASP: secure design and lifecycle considerations

---

## 🧭 Consequences

AegisPHP will support multiple runtimes without coupling the ecosystem to one model.

Runtime compatibility becomes a first-class concern,
and runtime adapters are treated as security-sensitive components.

This ADR ensures AegisPHP remains portable and enterprise-friendly.

---

🛡️ Stay behind the shield.
