# 🛡️ ADR-0001 — Security-First as a Foundational Constraint

- **Status:** Accepted
- **Date:** 2026-02-12
- **Authors:** AegisPHP Core Maintainers
- **Related:** MANIFEST.md, SECURITY.md

---

## 📌 Context

Modern PHP frameworks often optimize for development speed,
convenience, and developer experience.

While this accelerates delivery, it frequently introduces:

- Implicit behavior ("magic")
- Hidden coupling
- Runtime unpredictability
- Security as an optional concern

AegisPHP is intended for high-trust systems, critical APIs,
and enterprise-grade architectures where failure has significant impact.

Therefore, security cannot be a feature layered on top of the framework.
It must be a foundational constraint that influences all architectural decisions.

---

## 🎯 Decision

Security-first is declared a **non-negotiable architectural constraint**
for all AegisPHP modules.

This means:

1. Default behavior must fail closed.
2. Public contracts must be explicit and predictable.
3. Core abstractions must be immutable whenever possible.
4. No global state is allowed.
5. No hidden auto-discovery or reflection-based behavior in the core.
6. Security-relevant logic must live at the system boundary (middleware layer).
7. Breaking changes that improve security may be prioritized over convenience.

All new features must be evaluated through a security lens before acceptance.

---

## 🛡️ Security Considerations

This decision:

- Reduces attack surface by minimizing implicit behavior.
- Encourages explicit data flow and boundary control.
- Prevents accidental exposure of internal state.
- Promotes defensive defaults.

It does not eliminate vulnerabilities by itself,
but it establishes guardrails that reduce systemic risk.

---

## 🧱 Architectural Impact

- Influences all modules (core, bulwark, sigil, keep, etc.).
- Prohibits magic-driven architecture patterns.
- Enforces explicit dependency management.
- Shapes middleware-first request handling.

This decision constrains future design choices deliberately.

---

## ⚖️ Trade-offs

### Advantages

- Increased predictability
- Lower implicit risk
- Easier security auditing
- Enterprise credibility

### Disadvantages

- Slightly higher verbosity
- Less convenience compared to magic-heavy frameworks
- Potentially slower onboarding for beginners

---

## 🚀 Implementation Notes

- Core must remain minimal and immutable.
- Security defaults must be documented.
- PHPStan level 8+ required across core modules.
- All boundary layers must be testable.

Versioning impact: None (foundational declaration).

---

## 📚 References

- MANIFEST.md
- SECURITY.md
- Clean Architecture principles
- OWASP Secure Coding Practices

---

## 🧭 Consequences

All future architectural decisions must be compatible with this constraint.

If a feature conflicts with security-first philosophy,
it must be rejected or redesigned.

This ADR defines the identity of AegisPHP.

---

🛡️ Stay behind the shield.
