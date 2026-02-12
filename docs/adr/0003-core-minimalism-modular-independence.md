# 🛡️ ADR-0003 — Core Minimalism & Modular Independence

- **Status:** Accepted
- **Date:** 2026-02-12
- **Authors:** AegisPHP Core Maintainers
- **Related:** ADR-0001, ADR-0002, MANIFEST.md

---

## 📌 Context

Frameworks tend to grow over time.

Features accumulate.
Convenience layers expand.
Dependencies multiply.

What begins as a small core often evolves into a tightly coupled system
where the framework dictates the architecture of the application.

AegisPHP was designed with a different objective:

The core must remain small, stable, and independent.
All higher-level concerns must live outside of it.

---

## 🎯 Decision

AegisPHP adopts **Core Minimalism & Modular Independence** as a structural rule.

This means:

1. The `core` package must contain only:
   - HTTP abstractions
   - Middleware pipeline
   - Config + Env loader
   - Event bus
   - Runtime strategy abstraction

2. The core must NOT include:
   - Routing
   - Dependency injection
   - Authentication
   - Database access
   - Caching
   - Logging
   - Queue handling

3. Each module must be:
   - Installable independently via Composer
   - Usable without requiring other official modules
   - Architecturally replaceable

4. No module may force the use of another unless strictly justified and documented.

---

## 🛡️ Security Considerations

A minimal core reduces:

- Attack surface
- Hidden dependency chains
- Implicit behavior propagation
- Risk of privilege escalation through transitive coupling

By isolating infrastructure concerns into dedicated modules,
security boundaries remain clear and auditable.

---

## 🧱 Architectural Impact

- Encourages strict separation of concerns.
- Enables selective adoption.
- Prevents framework lock-in patterns.
- Keeps domain layer isolated from infrastructure.

The core becomes a stable foundation rather than a feature hub.

---

## ⚖️ Trade-offs

### Advantages

- Reduced cognitive load in the core
- Easier long-term maintenance
- Clear modular boundaries
- Enterprise-friendly architecture
- Flexible integration into existing systems

### Disadvantages

- Requires more explicit composition
- No "full-stack out-of-the-box" experience
- Slightly more setup for small applications

---

## 🚀 Implementation Notes

- New features must be evaluated: can this live outside core?
- Core dependency list must remain minimal.
- Public contracts in core should be treated as long-term stable APIs.
- Cross-module dependencies require documented justification.

Versioning impact:
- Breaking changes in core must be extremely rare and well-justified.

---

## 📚 References

- ADR-0001 — Security-First as a Foundational Constraint
- ADR-0002 — Zero-Magic Policy
- Clean Architecture principles
- UNIX philosophy: "Do one thing well"

---

## 🧭 Consequences

The AegisPHP ecosystem will grow horizontally (more modules),
not vertically (larger core).

The core must remain disciplined.

This ADR protects AegisPHP from architectural erosion over time.

---

🛡️ Stay behind the shield.
