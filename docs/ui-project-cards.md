# Projects view — card direction

Reference: 2TI Orchestrator → MAM Series (`/production/mam/series`).

The series card is the model for Nucleus project cards, with one explicit
correction from the owner: **do not force rows/columns to be equal, and do not
force every card to the same height.** Cards carry different amounts of
information (a project with 3 modules vs 12), so height follows content.

## Anatomy (from the reference)

```
┌──────────────────────────────────────────────────────────┐
│ SER-830                    [AUTO] [ACTIVE]               │  id chip · mode · status
│  ┌────┐  COLD LOGIC                                       │  logo · title
│  │logo│  @ColdLogicShow          ← handle/domain          │
│  └────┘  one-line description (clamped to 2 lines)       │
│                                                          │
│  MODULES  [Trailer workflow] [Unified assets] [+1]       │  chips, wrap, overflow
│                                                          │
│  ┌─────────┬─────────┬─────────┐                         │  3 mini stats
│  │EPISODES │YT SYNCED│SEO READY│                         │
│  │   22    │   22    │    0    │                         │
│  └─────────┴─────────┴─────────┘                         │
│  YouTube Distribution Sync                    100%       │  labelled progress
│  ████████████████████████████████████████                │
│  docu_serie                              Open Studio →  │  footer: type · action
└──────────────────────────────────────────────────────────┘
```

## Nucleus mapping

| Reference element | Nucleus value |
|---|---|
| `SER-830` chip | project slug / short id |
| `AUTO` / `MANUAL` | vhost mode: auto-generated vs custom vhost |
| `ACTIVE` / `PAUSED` / `PLANNING` | service state + git state, **state colour only** |
| logo | project favicon (already resolved by `helpers.php`) |
| title | project name |
| handle | `<name>.local` or configured hostname |
| description | framework + PHP/Node version, one line |
| MODULES chips | detected stack: Laravel / WordPress / static / Node · SSL · Git |
| 3 mini stats | services running · database size · last modified — or ports/SSL |
| progress | disk usage or SSL days-remaining, whichever is more actionable |
| footer | docroot path (truncated) · "Open" → `https://<name>.local` |

## Layout rules

- `display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));`
  with `align-items: start` — **never** `grid-auto-rows: 1fr`.
- Card height is `auto`; the grid row grows to the tallest card but cards do not
  stretch to it.
- Optional masonry (`column-count` / `columns`) if ragged edges look wrong; do
  not fake it with fixed heights.
- Description clamps at 2 lines, chips wrap with a `+N` overflow chip, so a card
  with few fields does not get padded out to match a rich one.
- Mini-stat row is fixed at 3 cells (they are comparable), the only equal-height
  element inside the card.

## Colour

Status is the only thing that gets accent colour:

```
.pill--ok   running / active   var(--success)
.pill--warn stopped / pending  var(--warning)
.pill--err  failed             var(--danger)
.pill--idle not configured     neutral
```

Stack chips stay neutral. No per-framework colours — otherwise a red "Laravel"
chip reads as an error.

## Implementation note

Build it as a **scoped component**, not a theme swap: a `partials/project-card.php`
plus rules in a new `assets/css/projects.css` loaded only on the projects page.
That keeps the blast radius to one view, so a visual mistake cannot take the
whole shell down the way the full theme swap did.
