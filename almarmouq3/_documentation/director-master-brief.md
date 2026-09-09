# Director Master Brief: Sovereign Scaffolding & Theme Normalization

**Project**: Al-Marmouq for Oud V3 (`http://almarmouq3.local/`)
**Target Path**: `/var/www/html/almarmouq3`
**Tenant**: #4 (Al-Marmouq for Oud)
**Stack**: Sovereign Laravel 10+, Blade, MySQL, Vanilla CSS (`_theme/dist` / 2Ti Sovereign Oud UI)
**Target Hosting**: Standard Shared Hosting Compatible (cPanel / DirectAdmin compatible PHP+MySQL)

---

## 🎯 Executive Director Mandate

The Director AI Agent is hereby instructed to execute the full end-to-end scaffolding, theme normalization, and code generation for **Al-Marmouq Sovereign CRM & ERP V3**.

All code must adhere strictly to the **Al-Marmouq UI Sovereign Specification** (`almarmouq-ui` skill & 2Ti Theme Tokens) and standard Laravel 10+ MVC/Service architectural patterns.

---

## 📐 Key Implementation Rules & Constraints

1. **WowDash Theme Drilling & Normalization (`/_theme`)**:
    - The Director **MUST drill** the `/var/www/html/almarmouq3/_theme/` directory (specifically `/var/www/html/almarmouq3/_theme/resources/views`) to analyze all component blade structures, widgets, forms, tables, and layouts.
    - Synthesize and output a standalone design token specification file: `/var/www/html/almarmouq3/_documentation/design-language.md`.
    - Replace generic Bootstrap / utility clutter with normalized Sovereign classes from `_theme/dist` (e.g., `.btn-primary-600`, `.card`, `.table-basic`, `.bg-neutral-50`, `.text-secondary-light`).

2. **Full Views, API, Controllers & Services Scaffolding**:
    - Every single navigation item defined in `resources/views/components/sidebar.blade.php` (and detailed in `Menu-Groups.md`) MUST have:
        - A dedicated **Blade View** rendered within the master layout (`resources/views/layouts/app.blade.php` or `master.blade.php`).
        - A dedicated **Laravel Controller** handling the page request, data fetching, and form submissions.
        - A dedicated **Domain Service** (in `app/Services/`) containing business logic, DB queries, and computations.
        - An **API Endpoint** (in `routes/api.php` and `app/Http/Controllers/Api/`) returning JSON responses for decoupled / AJAX updates.

3. **Shared Hosting & "AI Agent" Features Compatibility**:
    - Where the sidebar or menu mentions **"AI Agent"**, **"AI Assistant"**, or **"Automated Telemetry"**, these are **future implementations**.
    - **Crucial**: For this milestone, these features MUST be built using **standard Laravel Controllers and MySQL tables**. Do NOT rely on background daemons, Node services, or external Python sockets that require root VPS access. Everything must execute synchronously or via standard database-backed queues.

4. **Gitea VCS Repository Setup**:
    - Director must initialize and configure a Gitea repository for `almarmouq3` (e.g. `http://gitea.local/almarmouq/almarmouq3.git` or local gitea instance), push all initial code, and tag release `v1.0.0`.

---

## 🗺️ Execution Roadmap (7 Phases)

### Phase 1: Theme Drilling & Design Language Extraction

- Drill `/var/www/html/almarmouq3/_theme/` across all directories:
    - `views/dashboard`, `views/widgets`, `views/forms`, `views/table`, `views/componentspage`, `views/layout`.
- Build `/var/www/html/almarmouq3/_documentation/design-language.md` capturing layout grids, card structures, dark/light theme invariance, typography, and form inputs.

### Phase 2: Core Master Layout & Component Standardization

- Standardize `/var/www/html/almarmouq3/resources/views/layouts/master.blade.php` and `app.blade.php`.
- Ensure `sidebar.blade.php` is cleanly integrated with dynamic active-state highlighting based on current route names.

### Phase 3: Domain Service Layer & Migration Genesis

- Create migrations for core domain entities:
    - `clients`, `vip_profiles`, `quotes`, `tenders`, `inventory_items`, `curing_batches`, `distillation_formulas`, `procurement_orders`, `shipments`, `majlis_logs`, `commercial_rules`, `audit_trails`.
- Create corresponding Eloquent Models and Service classes under `app/Services/`.

### Phase 4: Route & Controller Scaffolding (Web & API)

- Scaffold web controllers in `app/Http/Controllers/`:
    - `CommandController`, `FinancialController`, `CrmController`, `CommunicationController`, `VaultController`, `ProcurementController`, `LogisticsController`, `PortalController`, `AdminController`.
- Scaffold API controllers in `app/Http/Controllers/Api/`.
- Register all routes in `routes/web.php` and `routes/api.php`.

### Phase 5: Blade View Generation & WowDash Normalization

- Create clean Blade views for all 40+ sidebar routes using normalized WowDash styles.
- Ensure all forms, data tables, filter toolbars, and modal popups use standard 2Ti / Al-Marmouq styling.

### Phase 6: Gitea Repository Integration

- Create Gitea repository `almarmouq3` on the local Gitea instance.
- Set up remote origin, commit all code, push `main` branch, and tag `v1.0.0-sovereign`.

### Phase 7: Verification & Smoke Testing

- Run Laravel route list check (`php artisan route:list`).
- Validate view rendering across all routes.
- Perform audit check for shared hosting readiness.

---

## 📌 Route Mapping Summary

| Module Group           | Routes to Scaffold                                                                                                                                                     | Primary Controller                       | Service Class                 |
| :--------------------- | :--------------------------------------------------------------------------------------------------------------------------------------------------------------------- | :--------------------------------------- | :---------------------------- |
| **Command**            | `command.overview`, `command.agenda`, `command.delegations`, `command.majlis`                                                                                          | `CommandController`                      | `ExecutiveCommandService`     |
| **Financial**          | `financial.overview`, `financial.pnl`, `financial.expenses`, `financial.tax`                                                                                           | `FinancialController`                    | `FinancialLedgerService`      |
| **CRM**                | `crm.cockpit`, `crm.vip-clients`, `crm.accounts`, `crm.tenders`, `crm.quotes.create`, `crm.customers`, `crm.sales`, `crm.pricing`                                      | `CrmController`, `QuoteController`       | `CrmEngagementService`        |
| **Communications**     | `communications.cockpit`, `communications.whatsapp`, `communications.webmail`, `communications.majlis-logs`, `communications.documents`                                | `CommunicationController`                | `ClientCommunicationService`  |
| **Vault / Atelier**    | `inventory.cockpit`, `inventory.finished-editions`, `inventory.catalog`, `inventory.price-history`, `inventory.custom-sets`, `inventory.waiting-list`, `inventory.coa` | `VaultController`, `InventoryController` | `HauteParfumerieVaultService` |
| **Procurement**        | `procurement.cockpit`, `procurement.orders`, `procurement.inbound`, `procurement.forecast`                                                                             | `ProcurementController`                  | `OudProcurementService`       |
| **Logistics**          | `logistics.cockpit`, `logistics.white-glove`, `logistics.courier`, `logistics.showcases`                                                                               | `LogisticsController`                    | `WhiteGloveLogisticsService`  |
| **Portal (Sanctuary)** | `portal.sanctuary`, `portal.allocations`, `portal.catalog`, `portal.bespoke-studio`, `portal.orders`, `portal.tracking`, `portal.certificates`                         | `PortalController`                       | `ClientSanctuaryService`      |
| **Administration**     | `admin.cockpit`, `admin.users`, `admin.roles`, `admin.commercial-rules`, `admin.audit`, `admin.settings`                                                               | `AdminController`                        | `SovereignAdminService`       |
