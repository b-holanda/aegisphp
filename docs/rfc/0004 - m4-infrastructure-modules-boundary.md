# RFC-0004 - M4 Infrastructure Boundary: Ledger, Vault, Relay, Watchtower

- **Status:** Proposed
- **Author(s):** AegisPHP Core Maintainers
- **Created:** 2026-02-12
- **Target Version:** 1.4 (M4)
- **Related ADRs:** ADR-0001, ADR-0002, ADR-0003, ADR-0004
- **Discussion Link:** TBD

---

## Summary

Define M4 infrastructure module boundaries with strict isolation:

- `aegisphp/ledger` (database access abstractions/adapters)
- `aegisphp/vault` (session/state storage abstractions/adapters)
- `aegisphp/relay` (queue/messaging abstractions/adapters)
- `aegisphp/watchtower` (observability and runtime-focused adapters)

The objective is to provide operational capabilities without violating core
minimalism or zero-magic policy.

---

## Motivation

As AegisPHP evolves, production systems need persistent data, sessions, queues,
and observability. These concerns are infrastructure-heavy and should not be
embedded into `core`.

Without explicit module boundaries, the ecosystem risks:

- hidden coupling
- unstable contracts
- framework lock-in
- security drift

---

## Detailed Proposal

### Ledger (`aegisphp/ledger`)

Public contracts (neutral names):

- `Connection`
- `Transaction`
- `QueryExecutor`

Rules:

- No Active Record pattern in module contracts.
- No domain entity coupling.
- Explicit transaction boundaries.

### Vault (`aegisphp/vault`)

Public contracts:

- `SessionStore`
- `Session`
- `StateStore`

Rules:

- Session/state ttl and rotation are explicit.
- Secure defaults for cookie/session metadata (where applicable via adapters).
- No implicit state sharing across requests.

### Relay (`aegisphp/relay`)

Public contracts:

- `Message`
- `Producer`
- `Consumer`
- `RetryPolicy`

Rules:

- Explicit acknowledgement and retry behavior.
- Deterministic serialization contracts.
- No hidden background worker orchestration in core.

### Watchtower (`aegisphp/watchtower`)

Public contracts:

- `MetricsCollector`
- `TraceContext`
- `HealthReporter`

Runtime relation:

- Runtime-aware adapters (including worker-based contexts) live outside core.
- Must protect against state leakage in long-lived workers.

---

## Security Considerations

- Aligns with ADR-0001 by preserving explicit boundaries around stateful subsystems.
- Aligns with ADR-0002 by disallowing hidden runtime detection/orchestration.
- Aligns with ADR-0004 by keeping runtime concerns in adapters, not core contracts.

Risks:

- Misconfigured adapters can leak secrets in logs/traces.
- Shared worker memory can leak request data.

Mitigation:

- explicit redaction hooks
- lifecycle reset hooks for worker adapters
- security integration tests per module

---

## Architectural Impact

- Core remains unchanged.
- Infrastructure concerns are split into independently installable modules.
- Optional dependencies belong in `suggest` where feasible.
- No mandatory coupling across Ledger/Vault/Relay/Watchtower.

---

## Alternatives Considered

1. Add DB/session/queue/observability directly to core
- Rejected: violates ADR-0003.

2. Single monolithic infrastructure package
- Rejected: reduces replaceability and increases coupling.

3. Convention-based auto-binding by folder naming
- Rejected: violates ADR-0002.

---

## Backward Compatibility

- Backward compatible for existing packages.
- SemVer impact: MINOR (new optional modules).
- No migration required for users not adopting M4 modules.

---

## Implementation Plan

1. Define contracts for each M4 module.
2. Add one minimal reference adapter per module.
3. Add tests for contract conformance and failure behavior.
4. Add worker-safety tests for Watchtower runtime adapters.
5. Publish migration and integration examples by module.

---

## Open Questions

- Should distributed tracing format support be OpenTelemetry-only initially?
- Should Vault baseline include encrypted session payload support in first release?
- Which queue backends should be prioritized for Relay reference adapters?

---

## References

- `docs/adr/0001-security-first-foundation.md`
- `docs/adr/0002-zero-magic-policy.md`
- `docs/adr/0003-core-minimalism-modular-independence.md`
- `docs/adr/0004-runtime-abstraction-layer.md`
- `README.md` roadmap section (M4)

---

## Acceptance Criteria

This RFC will be accepted if:

- [ ] Architectural alignment confirmed
- [ ] Security review completed
- [ ] Maintainer approval received
- [ ] Tests and benchmarks defined

