# 🛡️ Security Policy --- AegisPHP

## Security Philosophy

AegisPHP is designed as a **security-first framework**. Security is not
an optional feature --- it is part of the core design philosophy.

Our principles:

-   Fail closed by default
-   Explicit over implicit behavior
-   No hidden magic
-   Immutable core abstractions
-   Clear security boundaries

------------------------------------------------------------------------

## Supported Versions

Only the latest stable minor release series is actively supported with
security patches.

  Version   Supported
  --------- -----------
  1.x       ✅ Yes
  \< 1.0    ❌ No

Security fixes are released as patch versions (e.g., 1.2.3 → 1.2.4).

------------------------------------------------------------------------

## Reporting a Vulnerability

If you discover a security vulnerability, **do not open a public
issue**.

Instead, please report it privately:

📧 security@aegisphp.org\
(or open a private security advisory if using GitHub)

When reporting, include:

-   Description of the issue
-   Steps to reproduce
-   Potential impact
-   Suggested fix (if available)

You will receive acknowledgment within 48 hours.

------------------------------------------------------------------------

## Disclosure Policy

-   Vulnerabilities are investigated promptly.
-   If confirmed, a patch will be prepared.
-   A security advisory will be published after a fix is available.
-   Credit will be given to responsible reporters (unless anonymity is
    requested).

We follow responsible disclosure practices.

------------------------------------------------------------------------

## Scope

Security reports should relate to:

-   Core HTTP abstractions
-   Middleware pipeline behavior
-   Runtime emission logic
-   Security modules (bulwark, sigil, keep, sentinel)
-   Authentication, CSRF, rate limiting, or header handling
-   Unexpected exposure of sensitive data

Out of scope:

-   Issues in third-party libraries
-   Development environment misconfiguration
-   Unsupported versions

------------------------------------------------------------------------

## Security Best Practices

When using AegisPHP, we recommend:

-   Enabling HTTPS everywhere
-   Using strict CSP policies
-   Enforcing secure cookies
-   Rotating keys regularly
-   Avoiding global state
-   Running static analysis (PHPStan level 8+)
-   Keeping dependencies updated

------------------------------------------------------------------------

## Commitment

We are committed to:

-   Transparent communication
-   Timely security patches
-   Clear upgrade paths
-   Long-term architectural stability

Security is not a feature of AegisPHP.

It is its foundation.

------------------------------------------------------------------------

🛡️ Stay behind the shield.
