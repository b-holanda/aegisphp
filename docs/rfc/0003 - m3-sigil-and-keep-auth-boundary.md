# RFC-0003 - M3 Identity Boundary: Sigil + Keep

- **Status:** Proposed
- **Author(s):** AegisPHP Core Maintainers
- **Created:** 2026-02-12
- **Target Version:** 1.3 (M3)
- **Related ADRs:** ADR-0001, ADR-0002, ADR-0003, ADR-0004
- **Discussion Link:** TBD

---

## Summary

Define M3 identity and abuse-control boundary:

- `aegisphp/sigil` for authentication and authorization primitives
- `aegisphp/keep` for explicit rate limiting and abuse throttling

Goal: make secure identity flows explicit, auditable, and fail-closed.

---

## Motivation

After M2, applications still need a first-party way to:

- validate credentials/tokens explicitly
- apply constant-time secret verification
- throttle authentication attempts and high-risk endpoints

These concerns must remain modular and outside `core`.

---

## Detailed Proposal

### Sigil (`aegisphp/sigil`)

Public contracts (neutral names):

- `CredentialVerifier`
- `TokenVerifier`
- `Identity`
- `AuthContext`

Baseline behavior:

- Authentication middleware denies by default.
- Constant-time comparison for secret/token checks.
- Generic error messages for auth failures.
- No implicit session/global user context.

Implementation examples:

- `GatekeeperMiddleware`
- `SigilTokenVerifier`

### Keep (`aegisphp/keep`)

Public contracts (neutral names):

- `RateLimiter`
- `LimitPolicy`
- `CounterStore`
- `FingerprintStrategy`

Baseline behavior:

- Explicit policies (window, limit, burst).
- Deny with explicit `429 Too Many Requests` when exceeded.
- Deterministic key/fingerprint generation.
- Optional per-route and per-identity limits.

Implementation examples:

- `FortressLimiter`
- `SlidingWindowLimiter`

### Cross-module integration

- Sigil must support integrating Keep for auth endpoints by default guidance.
- Keep must not require Sigil for generic API throttling.
- Both modules remain independent installables.

---

## Security Considerations

- Aligns with ADR-0001 via deny-by-default auth and explicit throttling.
- Aligns with ADR-0002 via explicit wiring and no hidden context.
- Reduces credential stuffing and brute-force risk.

Risks:

- Weak fingerprint strategy can cause bypasses.
- Incorrect policy defaults can over-block or under-block.

Mitigation:

- test vectors for limiter precision
- defaults biased toward safer throttling
- documented tuning guidance by endpoint type

---

## Architectural Impact

- No core expansion.
- New modules for M3 concerns only.
- No coupling of domain models to authentication infrastructure.
- Runtime-agnostic behavior preserved.

---

## Alternatives Considered

1. Add auth/rate limiting to core
- Rejected: violates ADR-0003.

2. Rely on ad-hoc middleware snippets per project
- Rejected: inconsistent security posture and low auditability.

3. Use mutable global auth context
- Rejected: violates ADR-0001 and ADR-0002.

---

## Backward Compatibility

- Backward compatible for M0/M1/M2 users.
- SemVer impact: MINOR (new optional modules).
- No migration required for applications not adopting Sigil/Keep.

---

## Implementation Plan

1. Create `packages/sigil` with contracts and minimal auth middleware.
2. Create `packages/keep` with limiter contracts and baseline implementation.
3. Add integration tests for Sigil + Keep on auth endpoints.
4. Add security tests for constant-time checks and 429 behavior.
5. Document deployment tuning for distributed environments.

---

## Open Questions

- Should token format support be JWT-only initially, or token-agnostic by default?
- Which limiter algorithm should be the baseline: fixed window or sliding window?
- Should Keep include a memory store for dev/tests only in first release?

---

## References

- `docs/adr/0001-security-first-foundation.md`
- `docs/adr/0002-zero-magic-policy.md`
- `docs/adr/0003-core-minimalism-modular-independence.md`
- `docs/adr/0004-runtime-abstraction-layer.md`
- `README.md` roadmap section (M3)

---

## Acceptance Criteria

This RFC will be accepted if:

- [ ] Architectural alignment confirmed
- [ ] Security review completed
- [ ] Maintainer approval received
- [ ] Tests and benchmarks defined

