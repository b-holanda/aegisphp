# 🤖 AGENTS.md — AI Agent Guidelines for AegisPHP

This document defines how AI agents (LLMs, code assistants, automation bots)
must behave when generating code, documentation, or architectural proposals
for AegisPHP.

AI must reinforce the shield — never weaken it.

---

## 🛡️ Core Philosophy Alignment

All generated output must comply with:

- ADR-0001 — Security-First Constraint
- ADR-0002 — Zero-Magic Policy
- ADR-0003 — Core Minimalism & Modular Independence
- ADR-0004 — Runtime Abstraction Layer

If a suggestion violates these ADRs, it must be rejected.

---

## 🔐 Security-First Rules

AI-generated code MUST:

- Default to fail-closed behavior
- Avoid global state
- Avoid implicit behavior
- Avoid unsafe defaults
- Prefer immutable objects
- Explicitly define input/output contracts
- Avoid reflection-based auto-resolution in core

AI must never introduce:
- Hidden service containers
- Silent auto-discovery
- Implicit runtime detection
- Side-effect-based logic

---

## 🧱 Modular Discipline

When generating code:

- Never couple core to infrastructure modules
- Never introduce circular dependencies between packages
- Respect Composer-based independence
- Keep public contracts stable
- Avoid introducing large external dependencies by default

If suggesting third-party libraries:
- They must be optional
- Justification must be explicit

---

## ⚙️ Runtime Awareness

AI must:

- Avoid runtime-specific assumptions
- Respect the RuntimeStrategy abstraction
- Avoid storing mutable state in long-lived worker contexts
- Ensure no request data leaks between executions

Worker-based runtimes (RoadRunner/Franklin) require special care regarding:
- Memory persistence
- Static variables
- Singleton patterns

Avoid them.

---

## 🧠 Code Generation Principles

Generated code must:

- Use `declare(strict_types=1);`
- Define explicit return types
- Avoid `mixed` unless strictly necessary
- Prefer small focused classes
- Avoid over-engineering
- Remain framework-agnostic at the domain level

AI must not generate scaffolding structures that impose directory layout.

---

## 🧪 Testing Expectations

AI-generated features must:

- Include PHPUnit tests
- Be PHPStan level 8+ compatible
- Avoid reliance on global state in tests
- Support deterministic behavior

---

## 📚 Documentation Discipline

AI must:

- Update README when public APIs change
- Update ADR references when architectural shifts occur
- Avoid marketing exaggeration
- Prefer clarity over hype

---

## 🚫 Anti-Patterns (Forbidden)

AI must never suggest:

- Facades
- Global registries
- Hidden singletons
- Magic auto-wiring in core
- Convention-over-configuration in core
- Silent runtime detection
- Mutable HTTP abstractions

---

## 🎯 Decision Rule

If a design decision increases convenience but reduces:

- Security
- Predictability
- Explicitness
- Auditability

It must be rejected.

---

## 🛡️ Mission Reminder

AegisPHP is not built for scaffolding speed.

It is built for resilience.

AI agents must act as architects, not shortcut generators.

Stay behind the shield.
