# 🛡️ ADR-0002 — Zero-Magic Policy

- **Status:** Accepted
- **Date:** 2026-02-12
- **Authors:** AegisPHP Core Maintainers
- **Related:** ADR-0001, MANIFEST.md

---

## 📌 Context

Many modern frameworks rely on implicit behavior to improve developer experience:

- Automatic dependency injection via reflection
- Convention-based auto-discovery
- Implicit routing registration
- Hidden global containers
- Runtime mutation of objects

While convenient, these patterns often reduce predictability,
increase coupling, and complicate security auditing.

AegisPHP targets high-trust and critical systems where explicit control
is more valuable than implicit convenience.

---

## 🎯 Decision

AegisPHP adopts a **Zero-Magic Policy** in its core and official modules.

This means:

1. No implicit service registration.
2. No hidden global containers.
3. No auto-discovery in the core.
4. No runtime mutation of public contracts.
5. No reflection-based behavior in core execution paths.
6. No configuration inferred from directory structure.
7. All execution flow must be traceable through explicit code paths.

Optional convenience layers (if ever introduced) must live outside the core
and must never alter core behavior silently.

---

## 🛡️ Security Considerations

Implicit behavior increases:

- Hidden attack surfaces
- Unexpected execution paths
- Difficulty in auditing
- Risk of privilege escalation via misconfiguration

By enforcing explicit wiring and configuration:

- Attack surfaces are reduced.
- Data flow remains visible.
- Code is easier to reason about.
- Audits become straightforward.

This aligns directly with ADR-0001 (Security-First Constraint).

---

## 🧱 Architectural Impact

- Dependency injection must be explicit.
- Middleware registration must be explicit.
- Routing must be explicitly defined.
- Configuration must be declared, not inferred.

This discourages convention-over-configuration inside the core.

---

## ⚖️ Trade-offs

### Advantages

- Predictable execution
- Clear control boundaries
- Easier debugging
- Easier auditing
- Enterprise credibility

### Disadvantages

- Slightly more verbose setup
- Reduced "out-of-the-box" automation
- Steeper learning curve for developers used to magic frameworks

---

## 🚀 Implementation Notes

- Core must remain free from reflection-based auto-resolution.
- Any autowiring (e.g., in arsenal) must be optional and explicit.
- Public APIs must not rely on runtime mutation.
- Configuration loaders must remain deterministic.

Versioning impact: None (policy-level decision).

---

## 📚 References

- ADR-0001 — Security-First as a Foundational Constraint
- Clean Architecture
- OWASP Secure Design Principles

---

## 🧭 Consequences

All future contributions must respect this policy.

Features that introduce hidden behavior must be rejected or redesigned.

This ADR defines AegisPHP’s architectural discipline.

---

🛡️ Stay behind the shield.
