# AGENTS.md — Al-Marmouq For Oud (Sovereign CRM & ERP V3)


## Project Identity
- **Codebase**: `/var/www/html/almarmouq3` (Laravel 10+, PHP 8.3+, MySQL, Bootstrap 5/2TITHEME)
- **Domain**: Al-Marmouq For Oud — luxury oud perfumery sovereign CRM & ERP
- **Design Language Authority**: `design-language.md` + `_theme/` reference (2TITHEME Bootstrap 5 fork)
- **2TITHEME Tokens**: `#0D1B2A` (obsidian-base), `#162235` (obsidian-panel) — enforced across all views
- **CORTEX Bus**: `http://127.0.0.1:7880` (Ollama backend, qwen3.8-27b:q4_k_s Head Coder)
- **Model Team** (installed tags — NOT the model-team.json declarations):
  - **Director**: `deepseek-r1-8b:q4_k_m` (Q4_K_M, 4.8GB — active; `deepseek-r1:latest` is fallback)
  - **Head Coder**: `qwen3.8-27b:ud:q4_k_s` (27B, Q4_K_S, 15.2GB — installed; fits RTX 5070 Ti 16GB)
  - **Leg Worker**: `qwen2.5-coder:1.5b-base` (1.5B, Q4_K_M, 1.0GB)
  - **Leg Worker Alt**: `qwen2.5-coder-14b-instruct:q6_k` (14B, Q6_K, 11.5GB)
- **Director General**: `director_general.py` in `/home/zorin/.2ti/engines/2ti-cortex/` — meta-level oversight, model correction, lessons injection
- **Gitea/FORGE**: `http://localhost:3000` — rescue and checkpoint repository

> **Model Note**: All model-team.json declarations (`deepseek-r1-8b:q4_k_m`, `qwen3.8-27b:q4_k_s`) are now installed and active in Ollama. The Director General reconciles tags at dispatch time as a safety net. See `DIRECTOR_GENERAL_PROTOCOL.md` §5 for the full model selection matrix.

## 2TITHEME Naming Policy (Mandatory)
- The template known upstream as "WowDash" must **ALWAYS** be referred to as "2TITHEME" in all code, docs, commits, UI, reports, and conversations.
- Never flag, credit, or name "WowDash" anywhere.
- When 2TITHEME is detected, raw Bootstrap classes must be translated to 2TITHEME equivalents via the Conformity Compiler — never left raw.
- The deployed CSS artifact filename `wowdash.css` is kept for compatibility but must never appear in docs/reports; always say 2TITHEME.

## 7-Phase Delivery Methodology (Super_Prompt)
Phases are executed through the Director Control Panel, which dispatches tasks to the CORTEX API with the appropriate model role. The Director General corrects model tags before dispatch.

| Phase | Role | Model (installed tag) | Scope |
|-------|------|-----------------------|-------|
| 1 | Director | `deepseek-r1-8b:q4_k_m` | Reconnaissance & planning — NO file modifications. Produce gap matrix from Menu-Groups.md vs actual code. |
| 2 | Head Coder | `qwen3.8-27b:ud:q4_k_s` | Audit & debug — reproduce failures, fix confirmed defects only. |
| 3 | Head Coder | `qwen3.8-27b:ud:q4_k_s` | Code quality & architecture — refactor with evidence, optimize DB/API bottlenecks. |
| 4 | Head Coder + Vision | `qwen3.8-27b:ud:q4_k_s` / `gemma-4-e4b:latest` | UI/UX — enforce design-language.md compliance, 2TITHEME token invariance, dark/light parity. |
| 5 | Head Coder | `qwen3.8-27b:ud:q4_k_s` | Functional development — fill gaps from Menu-Groups.md, convert Route::view placeholders to controllers. |
| 6 | Leg Worker | `qwen2.5-coder:1.5b-base` | Testing & validation — unit, integration, route checks, asset builds, smoke tests. |
| 7 | Director | `deepseek-r1-8b:q4_k_m` | Deployment & delivery — requires explicit Director approval for commits/tags/deploy. |

## Lessons Bus Integration (Pre-Task Mandatory)
Before ANY phase task, the Director General queries the lessons bus:
1. **Have we delivered this before?** — CHROMA `agency_patterns` (category=execution) + MEM0 episodic memory
2. **How did we go about it?** — CHROMA `agency_patterns` (category=component_reuse, convention, language_convention)
3. **What environment gotchas apply?** — CHROMA `agency_patterns` (category=mission_failure) + MEM0 memory

Known failure patterns for the agency project (from CHROMA):
| Failure code | Frequency | DG response |
|---|---|---|
| `syntax_invalid` | 244 | Tighten prompt or use targeted edits; switch to `leg_worker_alt` (14b Q6) |
| `other` (unclassified) | 83 | Review `last_error` before retry |
| `reasoner_empty_output` | 65 | Switch model immediately |
| `target_missing` | 12 | Verify path before dispatch |

## Supervision Protocol
This project is under **Director General Supervision** via the CORTEX Director Control Panel.
- Tasks are dispatched asynchronously via CORTEX API (`/run/async`)
- Phase 1 retry is scheduled — previous attempts FAILED due to CORTEX restarts and HTTP 500 errors
- All task evidence, duration, and tool calls are logged to `cortex_tasks.json`
- The Director General monitors for: task timeouts (>900s), restart interruptions, low-quality results, model mismatches
- **Intervention triggers**: failed status, empty results, HTTP 500 (→ syntax_invalid lesson), HTTP 404 (→ target_missing lesson), model not installed
- **Gitea rescue**: every intervention checkpoints to `http://localhost:3000` before re-dispatch

## Reference Documents
- `Menu-Groups.md` — authoritative module/endpoint specification
- `design-language.md` — theme variable and component reference
- `_theme/` — complete 2TITHEME Laravel reference implementation
- `scaffold-roadmap.md` — phase checklist and progress tracking
- `findings-report.md` — audit health and Phase 1 failure analysis
- `implementation-planner.md` — 7-phase detailed plan with 2TITHEME tokens
- `/home/zorin/.2ti/engines/2ti-cortex/DIRECTOR_GENERAL_PROTOCOL.md` — DG oversight framework
