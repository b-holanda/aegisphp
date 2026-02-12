# RFC-0002 - M2 Hardening: Bulwark + Sentinel

- **Status:** Proposed
- **Author(s):** AegisPHP Core Maintainers
- **Created:** 2026-02-12
- **Target Version:** 1.2 (M2)
- **Related ADRs:** ADR-0001, ADR-0002, ADR-0003, ADR-0004
- **Discussion Link:** TBD

---

## Summary

Define M2 hardening scope:

- Stabilize `aegisphp/bulwark` security boundary behavior
- Introduce `aegisphp/sentinel` as an explicit, sanitized logging module

Goal: improve operational security and auditability without expanding `core`.

---

## Motivation

M0 provides secure core primitives, and Bulwark already provides security
middlewares. M2 needs two additional outcomes:

- consistent, testable hardening profiles in Bulwark
- structured and sanitized logging for security and incident response

Logging must be explicit and safe by default. Sensitive values must never be
stored raw.

---

## Detailed Proposal

### Bulwark stabilization

Baseline contract and behavior stabilization for:

- security headers
- payload limits
- method allowlists
- host allowlists
- crash shielding

Requirements:

- Fail closed defaults for suspicious or invalid input.
- Explicit status/body behavior (400/405/413/415/500 paths).
- No stack traces in non-debug mode.
- Deterministic middleware ordering in published profiles.

### Sentinel (`aegisphp/sentinel`)

Public contracts (neutral names):

- `Logger`
- `LogRecord`
- `LogSink`
- `RecordSanitizer`

Baseline behavior:

- Structured records (`level`, `message`, `context`, `timestamp`).
- Automatic redaction of sensitive keys:
  - `authorization`
  - `cookie`
  - `set-cookie`
  - `token`
  - `password`
  - `secret`
- Context-size limits to reduce accidental data leakage.
- No global logger singleton.

Implementation examples:

- `SentinelLogger`
- `ShieldedRecordSanitizer`
- `StreamLogSink`

Inter-module boundary:

- Bulwark must not require Sentinel.
- Sentinel may provide optional middleware adapters for error/event logging.

---

## Security Considerations

- Aligns with ADR-0001 by making defensive behavior and redaction default.
- Aligns with ADR-0002 by avoiding hidden logging setup and global state.
- Keeps operational logs useful while minimizing leakage risks.

Risks:

- Over-redaction can reduce forensic value.
- Under-redaction can leak secrets.

Mitigation:

- deterministic sanitizer rules
- configurable allow/deny lists
- dedicated security tests for known sensitive keys

---

## Architectural Impact

- No changes to `core` public contracts.
- `bulwark` remains focused on boundary security.
- `sentinel` is independent and optional.
- No circular dependency between modules.

---

## Alternatives Considered

1. Put logger abstractions in core
- Rejected: violates ADR-0003.

2. Reuse global PSR logger as default singleton
- Rejected: violates explicitness and no-global-state constraints.

3. Make Bulwark depend directly on Sentinel
- Rejected: violates modular independence.

---

## Backward Compatibility

- Backward compatible for `core` users.
- Mostly backward compatible for existing Bulwark usage.
- SemVer impact: MINOR for new Sentinel module and additive Bulwark hardening.

---

## Implementation Plan

1. Freeze and document Bulwark profile behavior.
2. Create `packages/sentinel` with contracts and minimal implementation.
3. Add sanitizer test suite with sensitive key fixtures.
4. Add optional integration examples (`core + bulwark + sentinel`).
5. Publish migration notes if any Bulwark profile behavior is normalized.

---

## Open Questions

- Should Sentinel expose a formal log schema version in v1?
- Should Bulwark crash middleware support pluggable reporter callbacks in baseline?
- Should PII classification policies be shipped in v1 or deferred?

---

## References

- `docs/adr/0001-security-first-foundation.md`
- `docs/adr/0002-zero-magic-policy.md`
- `docs/adr/0003-core-minimalism-modular-independence.md`
- `docs/adr/0004-runtime-abstraction-layer.md`
- `README.md` roadmap section (M2)

---

## Acceptance Criteria

This RFC will be accepted if:

- [ ] Architectural alignment confirmed
- [ ] Security review completed
- [ ] Maintainer approval received
- [ ] Tests and benchmarks defined

