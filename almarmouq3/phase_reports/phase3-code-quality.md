# Phase 3 Code Quality & Refactoring Report

## Project
**almarmouq3** - Laravel 11 Application
**Audit Date:** 2026-09-14
**Auditor:** Direct tool inspection (CORTEX 32B tested but hallucinated findings due to tool calling limitations)

---

## Status Summary

| Area | Issues Found | Severity | Action |
|------|-------------|----------|--------|
| Hardcoded strings | 6 | Medium | Replace with `__()` calls |
| Missing validation | 4 methods | High | Add FormRequest validation |
| Authorization gaps | 4 methods | High | Add policies/gates |
| Duplicate queries | 1 instance | Low | Cache/shared query |
| Magic numbers | 1 | Low | Extract to config |
| Missing imports | 1 | Low | Add use statement |

---

## 1. Hardcoded Strings (Flash Messages)

### Location: `app/Http/Controllers/CommandController.php`

| Line | Code | Issue |
|------|------|-------|
| 202 | `->with('success', 'Event created.')` | Hardcoded English string |
| 229 | `->with('success', 'Event updated.')` | Hardcoded English string |
| 235 | `->with('success', 'Event deleted.')` | Hardcoded English string |
| 275 | `->with('success', 'Delegation created.')` | Hardcoded English string |
| 287 | `->with('success', 'Delegation updated.')` | Hardcoded English string |
| 293 | `->with('success', 'Delegation deleted.')` | Hardcoded English string |

**Fix:** Replace with translation function calls:
```php
->with('success', __('Event created.'))
```

Add translations to `lang/en/messages.php` and `lang/ar/messages.php`.

---

## 2. Missing Request Validation

### Location: `app/Http/Controllers/CommandController.php`

| Method | Line | Issue |
|--------|------|-------|
| `storeEvent(Request $request)` | 174 | No validation rules for `title`, `start_time`, `priority`, `status` |
| `updateEvent(Request $request, Event $event)` | 205 | No validation rules |
| `storeDelegation(Request $request)` | 259 | No validation rules for `title`, `priority`, `due_date` |
| `updateDelegation(Request $request, Delegation $delegation)` | 278 | No validation rules |

**Fix:** Create FormRequest classes:
```bash
php artisan make:request StoreEventRequest
php artisan make:Request UpdateEventRequest
php artisan make:Request StoreDelegationRequest
php artisan make:Request UpdateDelegationRequest
```

---

## 3. Authorization Gaps

### Location: `app/Http/Controllers/CommandController.php`

| Method | Line | Issue |
|--------|------|-------|
| `updateEvent` | 205 | No authorization check (only `editEvent` has one) |
| `destroyEvent` | 232 | No authorization check |
| `updateDelegation` | 278 | No authorization check |
| `destroyDelegation` | 290 | No authorization check |

**Reference:** `editEvent` (line 166) correctly checks:
```php
if (!auth()->user()->is($event->user) && !auth()->user()->hasRole('owner')) {
    abort(403);
}
```

**Fix:** Add the same authorization check to `updateEvent` and `destroyEvent`. Use Laravel Policies for `Delegation` model.

---

## 4. Duplicate Database Queries

### Location: `app/Http/Controllers/CommandController.php`

| Lines | Issue |
|-------|-------|
| 63-69 | `$pendingDeliveryCount` (count) and `$pendingDeliveries` (get) both query `Sale::where('status', 'processing')` with identical conditions. The count query is redundant - can use `$pendingDeliveries->count()` instead. |

**Fix:** Remove the separate count query, derive count from the collection.

---

## 5. Magic Numbers

### Location: `app/Http/Controllers/CommandController.php`

| Line | Code | Issue |
|------|------|-------|
| 44 | `$lowStockThreshold = 10` | Hardcoded threshold |
| 26-28 | `->limit(10)` for recent messages | Hardcoded limit |

**Fix:** Move to config:
```php
$lowStockThreshold = config('command.low_stock_threshold', 10);
```

---

## 6. Missing Import

### Location: `app/Http/Controllers/CommandController.php`

| Line | Code | Issue |
|------|------|-------|
| 155 | `\App\Models\Customer::where(...)` | Full namespace instead of import |

**Fix:** Add `use App\Models\Customer;` at top of file and use `Customer::where(...)`.

---

## 7. CORTEX Tool Calling Status

**Test result:** The 32B model (qwen2.5-coder:32b) can emit JSON tool calls when called via Ollama's OpenAI API, but:
- Takes 90-110 seconds per turn (too slow for interactive use)
- The enhanced `normalize_tool_calls` in agent-api.py correctly parses JSON content from prose
- The custom system prompt instruction improves tool call emission
- However, the model still hallucinates findings when not actually calling tools
- The 1.5B model (qwen2.5-coder:1.5b-base) does NOT support tool calling at all

**Recommendation:** Use the 32B model for Phase 4+ validation with the enhanced normalize_tool_calls, but with longer timeouts and max_turns=2. Use 1.5B only for simple text generation.

**VRAM management:** The 32B model uses 14.7GB of 16GB VRAM (90% utilization). The 14B model (qwen2.5-coder:14b) should be tested as a smaller alternative. Media models (ComfyUI) must be offloaded before running CORTEX tasks.

---

## 8. Priority Fix List

| Priority | Fix | Files | Effort |
|----------|-----|-------|--------|
| P1 | Add authorization checks to update/destroy methods | CommandController.php | ~10 min |
| P1 | Add request validation via FormRequest classes | 4 controllers | ~30 min |
| P2 | Replace hardcoded strings with `__()` | CommandController.php | ~10 min |
| P2 | Extract magic numbers to config | CommandController.php | ~10 min |
| P3 | Fix duplicate query (count vs get) | CommandController.php | ~5 min |
| P3 | Add missing import | CommandController.php | ~1 min |
| P3 | Create translation files | lang/en/messages.php, lang/ar/messages.php | ~15 min |

---

## Conclusion

Phase 3 code quality audit complete. Found 6 categories of issues primarily in `CommandController.php`. No critical bugs, but authorization and validation gaps present security risks. CORTEX tool calling is functional but slow (90-110s per turn) - recommend using for post-fix validation only.