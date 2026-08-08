# Nucleus — Implementation Planner

> Translated from [findings-report.md](findings-report.md).
> Each **Phase** ends with a VERSION bump (`VERSION` file + `config.php` `APP_VERSION` + `CHANGELOG.md` entry) so every milestone is shippable.

**Baseline:** `1.0.1` → target **1.0.6**

---

## Phase 1 — Critical Fixes 🛡️ → **v1.0.2**

### Task 1.1 Restore CSRF verification
- [ ] 1.1.1 Add global `verifyCSRFToken(string $token): bool` wrapper in `includes/helpers.php` next to existing `getCSRFToken()`
- [ ] 1.1.2 Delegate to `\Nucleus\Core\Security::verifyCSRFToken()`
- [ ] 1.1.3 No signature change required — the 6 API files keep working

### Task 1.2 Restore real translation helper `t()`
- [ ] 1.2.1 Remove placeholder `t($key, $fallback)` from `includes/helpers.php` (~:1130)
- [ ] 1.2.2 Confirm `includes/i18n.php` registers the real `t()` after load order fix
- [ ] 1.2.3 Spot-check sidebar/dashboard still label correctly (en only is fine)

### Task 1.3 Fix dashboard "Ignore project" flow
- [ ] 1.3.1 `index.php:1032` change selector `project-card-container` → `project-card`
- [ ] 1.3.2 `index.php:1038` same for the empty-state reload check

### Task 1.4 Release v1.0.2
- [ ] 1.4.1 Bump `VERSION` file → `1.0.2`
- [ ] 1.4.2 Bump `config.php` `APP_VERSION` → `1.0.2`
- [ ] 1.4.3 Add `CHANGELOG.md` entry for 1.0.2

---

## Phase 2 — Windows-Port Artifact Removal → **v1.0.3**

### Task 2.1 `api/tools.php` — Linux binary discovery
- [ ] 2.1.1 Replace `findComposer()` Laravel `bin/composer/composer.phar` check → `composer` from PATH
- [ ] 2.1.2 Replace `findNPM()` Laravel `npm.cmd` check → `npm` from PATH, then `npx` fallback
- [ ] 2.1.3 Replace `findGit()` Laravel `git.exe` check → `git` from PATH
- [ ] 2.1.4 Delete the three now-unused functions and their `LARAGON_ROOT` branches

### Task 2.2 `api/backup.php` — Linux `mysqldump`
- [ ] 2.2.1 `findMySQLDump()` → search PATH for `mysqldump` then `mariadb-dump`
- [ ] 2.2.2 Update error string `'mysqldump.exe not found'` → `'mysqldump not found; install mariadb-client / mysql-client'`
- [ ] 2.2.3 Verify `createBackup()` DB path uses PATH binary correctly

### Task 2.3 `config.php` — legacy version/paths
- [ ] 2.3.1 `getAppVersion()` fallback `'4.0.0'` → `APP_VERSION` constant
- [ ] 2.3.2 Remove backslash-normalization `str_replace('\\','/')` in `saveDashboardPreferences()` + BASE_URL detection
- [ ] 2.3.3 Remove `LARAGON_DASHBOARD_PASSWORD` env fallback (keep `NUCLEUS_PASSWORD`)
- [ ] 2.3.4 Update stale comments referencing `Laragon-Dashboard`, `laragon-dashboard.local`, PHP built-in server

### Task 2.4 `includes/helpers.php` — stale Windows code
- [ ] 2.4.1 Fix `Version: 4.0.0` header → `1.0.x`
- [ ] 2.4.2 Rewrite PowerShell comment at `:789`
- [ ] 2.4.3 Remove dead `getLaragonVersion()` (`:241`)
- [ ] 2.4.4 Remove duplicate `getLaragonConfig()` (`:1093`) [config.php is canonical]

### Task 2.5 Rename "Laragon root" claims
- [ ] 2.5.1 Update user-facing errors in `api/services.php`, `api/tools.php`, `api/backup.php` → "Install root not defined"

### Task 2.6 Release v1.0.3
- [ ] 2.6.1 Bump version (VERSION, config.php, CHANGELOG)

---

## Phase 3 — Code Smell Reduction & Security Polish → **v1.0.4**

### Task 3.1 Collapse exception handling
- [ ] 3.1.1 `api/tools.php`, `api/backup.php`, `api/services.php`, `api/databases.php`, `api/env_editor.php`, `api/config.php`: `catch (Exception)` + `catch (Error)` → single `catch (\Throwable $e)`

### Task 3.2 Runtime cache dir correctness
- [ ] 3.2.1 `clearAllCaches()` (helpers.php:970) → target `cache/cache/` (i.e. `CACHE_ROOT`), drop dead `temp/` dirs

### Task 3.3 Remove dead `optimizeDatabases()` stub
- [ ] 3.3.1 grep usages first; if unused, remove from `helpers.php`

### Task 3.4 Sanitise language parameter
- [ ] 3.4.1 `partials/navbar.php`, `partials/sidebar.php:45`, `includes/i18n.php:27` — validate against `i18n/languages.php` whitelist before use

### Task 3.5 `.htaccess` hardening
- [ ] 3.5.1 Block `/data`, `/cache`, `/logs`, `/uploaded`, `.git`, dotfiles
- [ ] 3.5.2 Replace misleading `<FilesMatch>` extension list with explicit protection

### Task 3.6 Release v1.0.4
- [ ] 3.6.1 Bump version (VERSION, config.php, CHANGELOG)

---

## Phase 4 — UI / UX Polish → **v1.0.5**

### Task 4.1 `<head>` hygiene
- [ ] 4.1.1 Remove duplicate `<meta name="viewport">` (head.php:42)
- [ ] 4.1.2 Re-indent meta/OG block & language-selector consistency

### Task 4.2 Dashboard project grid actions
- [ ] 4.2.1 Remove fake "Share"/"Delete" links or route them to real actions
- [ ] 4.2.2 Delete → warn-and-`api/delete_project.php`; Share → copy project URL to clipboard with toast

### Task 4.3 Changelog accordion logic
- [ ] 4.3.1 Simplify `index.php` limit (`count($changelog) > 5`) into a clean `array_slice(…, 0, 5)`

### Task 4.4 Feedback polish
- [ ] 4.4.1 "Check for Updates" gets disabled state + spinner while running

### Task 4.5 Release v1.0.5
- [ ] 4.5.1 Bump version + CHANGELOG

---

## Phase 5 — Repo Hygiene, Docs & Delivery → **v1.0.6**

### Task 5.1 `.gitignore` completeness
- [ ] 5.1.1 Add `uploaded/`, `temp/`, `BeitNoura/` (untracked project dir), node marker dirs
- [ ] 5.1.2 Re-run `git status --porcelain` → confirm only Nucleus files + Intentional housekeeping

### Task 5.2 Docs sync
- [ ] 5.2.1 Update `README.md` version badge, platform support, contribution notes
- [ ] 5.2.2 Final `findings-report.md` / `planner.md` refresh

### Task 5.3 Final validation
- [ ] 5.3.1 `php -l` every modified script
- [ ] 5.3.2 Spot-check: dashboard renders, `t()`, CSRF flow, services action reaches real systemctl
- [ ] 5.3.3 `git status` clean-ish; stage, commit, tag `v1.0.6`

### Task 5.4 Release delivery
- [ ] 5.4.1 Commit & push to **local Gitea** (`origin`, `main`)
- [ ] 5.4.2 Push to GitHub `LebToki/Nucleus` (need remote added) if all pass
- [ ] 5.4.3 Restore npm of plan at end of every phase (CHANGELOG bump + tag + stable push)

---

## Future / deferred
- **F-1 (S-7)** Split 145KB `partials/scripts.php` into per-page modules (huge win, higher risk — queued for next minor)
- **F-2 (S-8)** Move `index.php` inline JS into `assets/js/`
- **F-3** Enable strict CSP (requires F-2)
- **F-4** Add real `optimizeDatabases()` backed by `Nucleus\Core\Databases`
- **F-5** Unit tests for `helpers.php` + `Core/Security.php`