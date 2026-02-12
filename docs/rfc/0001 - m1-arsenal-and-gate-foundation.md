# RFC-0001 - M1 Foundation: Arsenal + Gate

- **Status:** Proposed
- **Author(s):** AegisPHP Core Maintainers
- **Created:** 2026-02-12
- **Target Version:** 1.1 (M1)
- **Related ADRs:** ADR-0001, ADR-0002, ADR-0003, ADR-0004
- **Discussion Link:** TBD

---

## Summary

Introduce the M1 foundation modules:

- `aegisphp/arsenal` for explicit service composition
- `aegisphp/gate` for explicit HTTP routing

Both modules must remain optional and independent from `aegisphp/core`.

---

## Motivation

`aegisphp/core` intentionally does not include container or router behavior.
Applications still need explicit composition and route dispatch to move from core
building blocks to production applications.

The gap to solve:

- deterministic service wiring without hidden globals
- deterministic routing without annotations, auto-discovery, or directory conventions

This RFC closes that gap while preserving security-first and zero-magic constraints.

---

## Detailed Proposal

### Arsenal (`aegisphp/arsenal`)

Public contracts (neutral names):

- `Container`
- `Definition`
- `DefinitionSource`

Baseline behavior:

- Explicit registration only.
- Resolution by declared service id.
- No global container.
- No hidden singleton registry.
- No reflection in default execution path.

Optional behavior:

- Autowiring may exist only as explicit opt-in feature in `arsenal`.
- Autowiring must be disabled by default.
- Autowiring must never mutate core behavior or alter contracts silently.

Implementation examples (tank naming allowed for implementations only):

- `ArsenalContainer`
- `PhalanxDefinitionMap`

### Gate (`aegisphp/gate`)

Public contracts (neutral names):

- `Route`
- `RouteCollection`
- `RouteMatcher`
- `Dispatcher`

Baseline behavior:

- Explicit route declarations in code.
- Explicit method allowlist per route.
- Explicit parameter constraints.
- Deterministic matching order.
- 404 when no route matches.
- 405 when path matches but method is not allowed.

Forbidden behavior:

- Controller auto-discovery
- Annotation/attribute scanning as required behavior
- Convention-over-configuration route registration

Implementation examples:

- `CheckpointRouter`
- `BulwarkDispatcher`

### Runtime considerations

- Arsenal and Gate remain runtime-agnostic.
- Runtime-specific adaptation remains at `RuntimeStrategy` boundary.

---

## Security Considerations

- Aligns with ADR-0001 by keeping fail-closed behavior for unmatched/method-mismatched routes.
- Aligns with ADR-0002 by prohibiting implicit registration and discovery.
- No new global state is introduced.
- Request lifecycle remains explicit and auditable.

Potential risks:

- Misconfigured route tables may expose endpoints unintentionally.

Mitigation:

- deterministic matcher behavior
- route test fixtures for 404/405 semantics
- static analysis and explicit integration tests

---

## Architectural Impact

- Does not expand `core`.
- Preserves modular independence (ADR-0003).
- Adds optional packages for M1 only.
- Does not alter `RuntimeStrategy` contract (ADR-0004).

Compatibility:

- Backward compatible with current M0 core.

---

## Alternatives Considered

1. Add container and router directly into `core`
- Rejected: violates ADR-0003 core minimalism.

2. Use third-party container/router as official baseline
- Rejected: reduces control over zero-magic guarantees.

3. Annotation/attribute-driven auto-discovery
- Rejected: violates ADR-0002.

---

## Backward Compatibility

- Backward compatible for existing M0 users.
- SemVer impact: MINOR (new optional modules).
- No migration required for applications using only `core`.

---

## Implementation Plan

1. Create `packages/arsenal` with contracts, minimal implementation, and tests.
2. Create `packages/gate` with route contracts, matcher, dispatcher, and tests.
3. Add smoke tests composing `core + arsenal + gate`.
4. Add module README files with explicit wiring examples.
5. Add security test cases for 404/405 fail-closed behavior.

---

## Open Questions

- Should `gate` support host-based routing in baseline, or defer to a later RFC?
- Should optional autowiring in `arsenal` support scalar constructor args in v1?
- Should route caching be deferred until after behavior is fully stabilized?

---

## References

- `docs/adr/0001-security-first-foundation.md`
- `docs/adr/0002-zero-magic-policy.md`
- `docs/adr/0003-core-minimalism-modular-independence.md`
- `docs/adr/0004-runtime-abstraction-layer.md`
- `README.md` roadmap section (M1)

---

## Acceptance Criteria

This RFC will be accepted if:

- [ ] Architectural alignment confirmed
- [ ] Security review completed
- [ ] Maintainer approval received
- [ ] Tests and benchmarks defined

