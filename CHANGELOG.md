# Changelog

All notable changes to Nucleus will be documented in this file.

## [1.2.0] - 2026-08-08

### Nodes & Plugins
- **Plugins as node installers with user-provided root access** — installing a service node (e.g. Mailpit) now detects when root is required and prompts for the sudo password. The password is used only for the install commands via `sudo -S`, never stored or logged; a temporary 0600 file is wiped immediately after use.
- **Passwordless-sudo detection** — if the web user already has a `NOPASSWD` sudoers rule, node installs run without any prompt.
- **Dual-scope detection** — plugins are detected as *System service*, *User service*, or *Detected (running)* (port probe), so already-running instances are never shown as "Not Installed".
- **Elevated uninstall/start/stop** — service operations reuse the same elevation flow instead of blindly calling `sudo`.

### Mail Services (Linux)
- **Mailpit support** — installable as a systemd node from the Plugins page; the Mailbox page now shows live CONNECTED/OFFLINE status and loads emails through the Mailpit API.
- **SMTP tooling** — php.ini discovery now works on Linux (`/etc/php/*/{apache2,cli}/php.ini`), with MTA-aware configuration (Mailpit `sendmail` vs Postfix `/usr/sbin/sendmail -t -i`).
- **Dark-mode fixes** — Mailbox service banner and status text now use theme-native classes (`text-secondary-light`, `bg-neutral-50`) instead of Bootstrap-standard ones.

### UI / Theme
- **Changelog page cleanup** — no standard font classes or inline font overrides; accordion and buttons restyled with theme-native classes.
- **Databases page fix** — restored missing structural `</div>` so the footer pins to the bottom again.

### Housekeeping
- `audiencepulse/` and `BeitNoura/` are separate projects and are now untracked/ignored.

## [1.0.2] - 2026-08-08

### Critical Fixes
- **Restored CSRF verification** — added the missing global `verifyCSRFToken()` helper (`includes/helpers.php`). Six API endpoints (`services`, `databases`, `plugins`, `env_editor`, `backup`, `config`) were calling an undefined global function, which made every destructive action (service start/stop/restart, database create/delete, plugin install, .env save, config save) fail with a fatal error. Now delegated to `Nucleus\Core\Security::verifyCSRFToken()`.
- **Restored translations** — removed a placeholder `t()` in `includes/helpers.php` that shadowed the real i18n implementation in `includes/i18n.php`. All UI strings (en + 7 locales) now translate again instead of falling back to raw keys.
- **Fixed dashboard "Ignore project"** — the ignore handler targeted `.project-card-container` while cards render as `.project-card`; the removal animation and empty-state reload on the dashboard now work (`index.php`).

## [1.0.1] - 2026-08-05

### Security & Hardening
- **Removed hardcoded fallback password** — admin password now resolved via env var (`NUCLEUS_PASSWORD`), auto-generated file (`data/admin_password.txt`), or random fallback. No more `ChangeThisPassword123!`.
- **Namespace migration** — all core classes moved from `LaragonDashboard\*` to `Nucleus\Core\*` to eliminate legacy branding.
- **Typed security methods** — CSRF, auth, and session helpers now use strict return types.
- **HTTPS-aware auth model** — localhost auto-auth on HTTP; password always enforced over HTTPS or when `AUTH_SHARED_WORKSPACE=true`.

### Plugins System
- **phpMyAdmin as downloadable plugin** — no longer bundled in the repo. Install/uninstall from the Plugins page; downloads v5.2.2 on demand into the dashboard root.
- **Webapp plugin type** — PluginManager now supports both `binary` (systemd services like Mailpit) and `webapp` (download-and-extract tools like phpMyAdmin) plugin types.

### Infrastructure
- **phpMyAdmin removed from git** — 2,274 third-party files untracked; added to `.gitignore` as a runtime download.
- **Cleaned .gitignore** — fixed case-sensitive symlink handling (`SAS`/`sas`), added runtime download exclusions.
- **Login page** — new standalone `login.php` with proper auth flow, error handling, and redirect logic.

---

## [1.0.0] - 2026-08-03

### Nucleus v1.0.0 Release
- **Linux-Native Engine**: Native Linux architecture tailored for ZorinOS, Ubuntu, and Debian distributions.
- **Systemd & Service Management**: Monitored and managed systemd services (Apache2, MariaDB/MySQL, PHP-FPM, Postfix).
- **Virtual Host & Project Discovery**: Scans `/var/www/html/` and `/etc/apache2/sites-enabled/` automatically.
- **phpMyAdmin & Database Integration**: Writable `tmp/` caching with passwordless root MySQL connection.
- **Dynamic Log & Vitals Monitoring**: Tails Apache error/access logs, PHP-FPM logs, MariaDB logs, and 2TI Orchestrator logs.
- **2TI Orchestrator Ecosystem**: Seamless UI/UX porting, MAM suite, Intelligence suite, and Inertia.js React frontend.
