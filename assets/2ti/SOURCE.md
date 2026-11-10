# Vendored: 2TI theme

Source: /var/www/html/2ti-theme (git commit 95718e9 — "2TI theme v0 — a replacement
for the licensed WowDash template").

Vendored by pinned copy, not by reference, so Nucleus builds without the theme repo
checked out. Re-vendor deliberately; do not edit these files in place.

- tokens.css — colour/space/type tokens, light in :root, dark in [data-theme="dark"]
- theme.css  — components (nav, buttons, forms, cards, tables, badges, stats, tabs,
               modal, toast, empty state)
- theme.js   — 3 KB, no deps: theme + direction persistence
