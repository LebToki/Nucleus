# Nucleus — Findings Report

**Date:** 2026-08-08
**Version under audit:** 1.0.1
**Scope:** Nucleus core (config, includes/, api/, pages/, partials/, index.php, router/.htaccess). Excludes tracked monorepo sub-app `audiencepulse/`, vendor `assets/` libs, and runtime-only dirs (`phpmyadmin/`).

This report documents every issue found during the codebase audit. It feeds directly into [planner.md](planner.md), which converts these findings into phased tasks with version bumps.

---

## Severity Legend

| Severity | Meaning |
|----------|---------|
| 🔴 **Critical** | Broken behaviour in production path |
| 🟠 **Windows artifact** | Leftover from the Laragon-Dashboard (Windows) port |
| 🟡 **Code smell** | Maintainability / duplication / dead code |
| 🔵 **UI/UX** | Visual or interaction defects |
| ⚪ **Doc/git** | Documentation or repository hygiene |

---

## 1. Critical Bugs

### CR-1 · Undefined `verifyCSRFToken()` breaks every destructive API action
**Severity:** 🔴 Critical

Six API endpoints call a **global** function `verifyCSRFToken()` that is never defined. Only the namespaced `\Nucleus\Core\Security::verifyCSRFToken()` exists (`includes/Core/Security.php:38`). The `if (!verifyCSRFToken($token))` checks therefore throw a fatal `Uncaught Error: Call to undefined function verifyCSRFToken()` every time — meaning **none of these actions work**:

- `api/services.php:38` — service `start` / `stop` / `restart` / `reload`
- `api/databases.php:34` — DB `create` / `delete`
- `api/plugins.php:29` — plugin install/uninstall lifecycle
- `api/env_editor.php:60` — `.env` save
- `api/backup.php:39` — backup `backup_project` / `backup_database` / `delete_backup`
- `api/config.php:113` — config editor save

The `getCSRFToken()` wrapper already exists in `includes/helpers.php:20`; a matching `verifyCSRFToken()` wrapper is missing.

### C-2 · `t()` translation helper is shadowed by a placeholder
**Severity:** 🔴 Critical

`includes/helpers.php:1130` defines a stub `function t($key, $fallback = '')` guarded by `if (!function_exists('t'))`. Because `helpers.php` is loaded *before* the real `t()` in `includes/i18n.php:164` (guarded the same way), the placeholder wins and **all UI translations silently render fallback keys**. Modules of the app (dashboard, sidebar, services, etc.) therefore show English keys instead of the 8 supported languages.

Fix: remove the placeholder `t()` from `helpers.php` so `i18n.php` registers the real implementation.

### C-3 · Dashboard "Ignore project" action is broken
**Files:** `index.php:398` vs `index.php:1032`, `index.php:1038`

Project cards are rendered with class `project-card` (`index.php:398`) but the ignore handler removes elements with class `project-card-container` (`index.php:1032`, `index.php:1038`). After a successful ignore the card is never removed and the empty-state reload never fires. (Fix already exists in `pages/projects.php:470` which uses the correct `project-card` selector.)

---

## 2. 🟠 Windows-Port Artifacts

### W-1 · `api/tools.php` — Windows-only binary discovery
**File:** `api/tools.php`

- `findComposer()` (`:47`) checks `$laragonRoot/bin/composer/composer.phar`
- `findNPM()` (`:64`) checks `$laragonRoot/bin/nodejs/node-*/npm.cmd`
- `findGit()` (`:87`) checks `$laragonRoot/bin/git/cmd/git.exe`

All three fall back to the global command, so they work, but the Laragon/Windows branches are dead code that performs a pointless `glob()`/`file_exists()` scan on every request for `composer`/`git`. On Linux the global PATH binary is correct.

### W-2 · `api/backup.php` — `mysqldump.exe` lookup
**File:** `api/backup.php`

- `findMySQLDump()` (`:60`) globs `$laragonRoot/bin/mysql/mysql-*/bin/mysqldump.exe`
- Error messages reference the Windows binary: `'mysqldump.exe not found'` (`:184`)

On Linux the `mysqldump` binary lives on PATH (`/usr/bin/mysqldump` or `mariadb-dump`), not under a Laravel bin tree.

### W-3 · `config.php` — version & path leftovers
**File:** `config.php`

- `getAppVersion()` fallback returns `'4.0.0'` (`:480`) — the Windows Laravel-Dashboard version
- Backslash-to-slash normalisation `str_replace('\\', '/', ...)` for paths (`:313`, `:316`) is Windows-specific; Linux paths never contain backslashes
- Massive multi-method `BASE_URL`/`ASSETS_URL` resolution (`:138-252`) with comments referencing `Laragon-Dashboard`, `laragon-dashboard.local`, PHP built-in server and "Laravel auto-vhost" — everything the Linux deployment doesn't need
- Reference to `LARAGON_DASHBOARD_PASSWORD` env fallback (`:80`)

### W-4 · `includes/helpers.php` — stale comments & dead functions
**File:** `includes/helpers.php`

- `:789` comment *"avoid slow powershell subprocess creation"* (Bolt-generated, Windows-era)
- `getLaragonVersion()` (`:241`) returns literal `'Nucleus'` — unused leftover
- File header `Version: 4.0.0` (`:4`) — reports the old Laravel version inside the Linux codebase
- `getLaragonConfig()` duplicate (`:1093`) shadowed by the `config.php` version — dead code

### W-5 · `api/*.php` — "Laragon root not defined" error strings
**Files:** `api/services.php:50`, `api/tools.php:36`, `api/backup.php:49`

User-facing errors still say *"Laragon root not defined"* on a Linux-only product.

---

## 3. 🟡 Code Smells

### S-1 · Duplicated preference functions
`getDashboardPreferences()` / `saveDashboardPreferences()` are defined in BOTH `config.php:361/301` and `includes/helpers.php:903/943`; `getLaragonConfig()` in both `config.php:380` and `helpers.php:1093`. Guards prevent fatals, but create two sources of truth.

### S-2 · `t()` placeholder in helpers (see C-1) — also duplicate logic of i18n

### S-3 · `catch (Exception)` + `catch (Error)` duplicated
**Files:** `api/tools.php:362-378`, `api/backup.php:450-466`, `api/services.php`, `api/databases.php`, `api/env_editor.php`, `api/config.php`

Six files repeat an identical, verbose catch block. Collapsible to `catch (\Throwable $e)`.

### S-4 · `clearAllCaches()` points at non-existent dirs
**File:** `includes/helpers.php:972`, `:983` — clears `temp/cache` and `temp/sessions`, but the app uses `cache/` (see `index.php` bootstrap and Core\Cache). Dead paths.

### S-5 · Placeholder `optimizeDatabases()`
**File:** `includes/helpers.php:1020` — always returns `success: false, message: 'requires MySQL credentials'`. Dead/broken placeholder.

### S-6 · Repeated defensive shell wrapper
Every `getApacheVersion`-style helper repeats `@shell_exec('x 2>&1')` with a two-branch fallback. Fine, but version parsing could be centralised.

### S-7 · `scripts.php` monolith
**File:** `partials/scripts.php` — **3,387 lines / 145KB** single asset loaded on every page. Duplicates:
- `escapeHtml()` defined 2× (`:471`, `:1009`)
- theme/content logic 3×
- All page JS loads for all users regardless of page.

  (Deferred to a later release — see planner Phase 9.)

### S-8 · `index.php` inline `<script>` blocks
`index.php` embeds large inline JS after the layout (project wizard, ignore, service monitor) instead of the external `scripts.php`. Inconsistent with the "Keep JS in assets/js/" guideline in `README.md`.

### S-9 · `getChangelog()` limit condition
`index.php:522` `if (count($changelog) > 5 && !$first) break;` — works by accident but reads confusingly.

---

## 4. 🔵 UI / UX Defects

### U-1 · Duplicate `<meta name="viewport">`
**File:** `partials/head.php:18,42` — rendered twice in `<head>`.

### U-2 · Non-functional "Share" / "Delete" buttons
**File:** `index.php:462-472`
Both buttons just link to `index.php?page=projects`. Delete should be a real (confirmed) delete; Share could copy the project URL.

### U-3 · Unsanitised `$_GET['lang']`
**Files:** `partials/navbar.php`, `partials/sidebar.php:45`, `includes/i18n.php:27`
Language is read directly from the query string with no whitelist validation at navigation time (i18n's `set_current_language()` does validate later).

### U-4 · Mixed indentation in `head.php` (lines 19-52 are unindented from the template body)

### U-5 · Dashboard "Check for Updates" button — checks every browser reload; no cooldown feedback

### U-6 · Hardcoded `http://localhost/phpmyadmin` link (`index.php:326`) not covered by CSP connect-src (minor)

---

## 5. ⚪ Documentation & Repository Hygiene

### D-1 · `.gitignore` drift
The web root `/var/www/html` carries unrelated project directories. Current `.gitignore` (already updated 2026-08-05) covers `2ti-orchestrator/`, `dashboard/`, `worldmonitor/`, `BeitNoura/`, `2tinteractive`, `SAS`, `phpmyadmin/`, `promo/` — but missing:

- `uploaded/` — runtime backup dir created by `api/backup.php:126`
- `Beitn./Noura/` currently untracked → **shows in `git status`**

### D-2 · Stray untracked/mixed changes in `audiencepulse/` subrepo
`audiencepulse` is part of the monorepo; its `includes/functions.php`, `wowdash-admin/chat-message.html` have local edits, plus untracked `admin/assets/images/users/default.png` and `wowdash-admin/includes/`. Must not be swept into Nucleus fix commits.

### D-3 · README hardcodes `7.0.1`? No — README table says PHP 8.1+, version badges list `1.0.1`.

### D-4 · CHANGELOG title says "Nucleus Sovereign Platform" (older naming) vs README "Nucleus".

---

## 6. Security Notes (informational, no action yet)
- CSP allows `unsafe-inline`/`unsafe-eval` for inline scripts — acceptable for current architecture; revisit if S-8 is executed.
- `.htaccess` `<FilesMatch>` lists extensions but does not block `.git`, `data/`, `cache/`.
- Auth model (HTTP-localhost auto-auth) is intentional; documented.

---

*Generated by the Nucleus audit — this document is the input to `planner.md`.*