# 🤝 Contributing to AegisPHP

Thank you for considering contributing to AegisPHP.

Aegis is built with a security-first, minimalist philosophy. Every
contribution should respect its core principles: predictability,
explicitness, immutability, and architectural discipline.

------------------------------------------------------------------------

## 🛡️ Guiding Principles

Before contributing, ensure your changes:

-   Do not introduce magic or hidden behavior
-   Do not create global state
-   Do not tightly couple domain logic to infrastructure
-   Preserve immutability in core abstractions
-   Respect modular boundaries

Security and clarity always take priority over convenience.

------------------------------------------------------------------------

## 📦 Monorepo Structure

All official packages live under:

packages/

Each package must remain independently installable via Composer.

Do not introduce cross-package dependencies unless explicitly justified
and documented.

------------------------------------------------------------------------

## 🧪 Quality Requirements

All contributions must:

-   Pass PHPUnit tests
-   Pass PHPStan (high level, preferably 8+)
-   Follow PSR-12 coding standards
-   Include tests for new functionality
-   Avoid breaking public interfaces without version bump justification

------------------------------------------------------------------------

## 🧱 Branching Strategy

-   `main` → stable branch
-   Feature branches → `feature/<name>`
-   Fix branches → `fix/<name>`

Open Pull Requests against `main`.

------------------------------------------------------------------------

## 🔍 Pull Request Guidelines

A Pull Request must include:

-   Clear description of changes
-   Reasoning behind architectural decisions
-   Tests covering the new behavior
-   Documentation updates if applicable

Avoid large, unrelated changes in a single PR.

------------------------------------------------------------------------

## 🔐 Security Contributions

If your contribution relates to security:

-   Clearly describe threat scenarios
-   Explain how the fix mitigates the risk
-   Ensure backward compatibility when possible

For vulnerabilities, follow the process described in `SECURITY.md`.

------------------------------------------------------------------------

## 📚 Documentation

Good documentation is as important as good code.

If your change affects public APIs or behavior, update the relevant
README files accordingly.

------------------------------------------------------------------------

## 🧭 Code Style

-   `declare(strict_types=1);` in all PHP files
-   Explicit return types
-   Avoid `mixed` unless absolutely necessary
-   Prefer small, focused classes
-   Keep core minimal

------------------------------------------------------------------------

## 🚀 Roadmap Alignment

Before implementing large features, check alignment with the roadmap:

M0 --- Core\
M1 --- Arsenal + Gate\
M2 --- Bulwark + Sentinel\
M3 --- Sigil + Keep\
M4 --- Ledger + Vault + Relay + Watchtower

Major architectural changes should be discussed before implementation.

------------------------------------------------------------------------

## 🛡️ Philosophy Reminder

AegisPHP is not designed for rapid scaffolding or magic-driven
development.

It is built for systems that require resilience, predictability, and
explicit control.

Every contribution must strengthen the shield --- not weaken it.

------------------------------------------------------------------------

Thank you for helping improve AegisPHP.

Stay behind the shield.
