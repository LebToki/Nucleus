# Changelog

All notable changes to Nucleus Sovereign Platform will be documented in this file.

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
