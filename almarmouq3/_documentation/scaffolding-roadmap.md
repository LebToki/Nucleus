# Al-Marmouq V3 Sovereign Scaffolding & Component Roadmap
**VHost**: `http://almarmouq3.local/`
**Location**: `/var/www/html/almarmouq3`
**Menu Specs Source**: [Menu-Groups.md](file:///var/www/html/almarmouq3/Menu-Groups.md)
**Sidebar Navigation Source**: [sidebar.blade.php](file:///var/www/html/almarmouq3/resources/views/components/sidebar.blade.php)

---

## 🛠️ Complete Scaffolding Directory Structure

### 1. Controllers (`app/Http/Controllers/`)
- `CommandController.php` (Overview, Daily Agenda, Delegations, Majlis Calendar)
- `FinancialController.php` (Overview Cockpit, P&L Statement, COGS & Expenses, Tax Settlements)
- `CrmController.php` (Cockpit, VIP Clients, Commercial Accounts, Sovereign Tenders, Customer Ledger, Price Tiers)
- `QuoteBuilderController.php` (Bespoke Oud Quote Builder & Assembly)
- `CommunicationController.php` (Communications Cockpit, WhatsApp Dispatch, Webmail Sync, Majlis Session Logs, Document Vault)
- `VaultController.php` (Vault Telemetry, Finished Editions, Product Catalog, Price History, Bespoke Assembly, VIP Waitlist, COA Provenance)
- `ProcurementController.php` (Procurement Cockpit, Supplier Purchase Orders, Inbound Customs Shipments, Replenishment Forecast)
- `LogisticsController.php` (Logistics Cockpit, White-Glove Escort Delivery, Courier Tracking, Diplomatic Showcase Transport)
- `PortalController.php` (Client Sanctuary Overview, Private Allocations, Reserve Catalog, Bespoke Studio, Orders & Quotes, Delivery Tracking, Vault Certificates)
- `AdminController.php` (Admin Cockpit, Team User Management, RBAC Roles & Permissions, Commercial Rule Engine, System Audit Trail, Boutique Mail & System Settings)

### 2. API Controllers (`app/Http/Controllers/Api/`)
- `Api/CommandApiController.php`
- `Api/FinancialApiController.php`
- `Api/CrmApiController.php`
- `Api/VaultApiController.php`
- `Api/ProcurementApiController.php`
- `Api/LogisticsApiController.php`
- `Api/PortalApiController.php`

### 3. Service Layer (`app/Services/`)
- `ExecutiveCommandService.php`
- `FinancialLedgerService.php`
- `CrmEngagementService.php`
- `ClientCommunicationService.php`
- `HauteParfumerieVaultService.php`
- `OudProcurementService.php`
- `WhiteGloveLogisticsService.php`
- `ClientSanctuaryService.php`
- `SovereignAdminService.php`

### 4. Blade Views Scaffolding (`resources/views/`)
- **Command**:
  - `resources/views/command/overview.blade.php`
  - `resources/views/command/agenda.blade.php`
  - `resources/views/command/delegations.blade.php`
  - `resources/views/command/majlis.blade.php`
- **Financial**:
  - `resources/views/financial/overview.blade.php`
  - `resources/views/financial/pnl.blade.php`
  - `resources/views/financial/expenses.blade.php`
  - `resources/views/financial/tax.blade.php`
- **CRM**:
  - `resources/views/crm/cockpit.blade.php`
  - `resources/views/crm/vip-clients.blade.php`
  - `resources/views/crm/accounts.blade.php`
  - `resources/views/crm/tenders.blade.php`
  - `resources/views/crm/quotes/create.blade.php`
  - `resources/views/crm/customers.blade.php`
  - `resources/views/crm/sales.blade.php`
  - `resources/views/crm/pricing.blade.php`
- **Communications**:
  - `resources/views/communications/cockpit.blade.php`
  - `resources/views/communications/whatsapp.blade.php`
  - `resources/views/communications/webmail.blade.php`
  - `resources/views/communications/majlis-logs.blade.php`
  - `resources/views/communications/documents.blade.php`
- **Vault (Inventory)**:
  - `resources/views/inventory/cockpit.blade.php`
  - `resources/views/inventory/finished-editions.blade.php`
  - `resources/views/inventory/catalog.blade.php`
  - `resources/views/inventory/price-history.blade.php`
  - `resources/views/inventory/custom-sets.blade.php`
  - `resources/views/inventory/waiting-list.blade.php`
  - `resources/views/inventory/coa.blade.php`
- **Procurement**:
  - `resources/views/procurement/cockpit.blade.php`
  - `resources/views/procurement/orders.blade.php`
  - `resources/views/procurement/inbound.blade.php`
  - `resources/views/procurement/forecast.blade.php`
- **Logistics**:
  - `resources/views/logistics/cockpit.blade.php`
  - `resources/views/logistics/white-glove.blade.php`
  - `resources/views/logistics/courier.blade.php`
  - `resources/views/logistics/showcases.blade.php`
- **Portal**:
  - `resources/views/portal/sanctuary.blade.php`
  - `resources/views/portal/allocations.blade.php`
  - `resources/views/portal/catalog.blade.php`
  - `resources/views/portal/bespoke-studio.blade.php`
  - `resources/views/portal/orders.blade.php`
  - `resources/views/portal/tracking.blade.php`
  - `resources/views/portal/certificates.blade.php`
- **Admin**:
  - `resources/views/admin/cockpit.blade.php`
  - `resources/views/admin/users.blade.php`
  - `resources/views/admin/roles.blade.php`
  - `resources/views/admin/commercial-rules.blade.php`
  - `resources/views/admin/audit.blade.php`
  - `resources/views/admin/settings.blade.php`

---

## 🎨 Theme Normalization Guidelines (`/_theme/`)

The Director AI Agent must drill `/var/www/html/almarmouq3/_theme/resources/views` and extract:
1. Card components (`.card`, `.card-header`, `.card-body`, `.card-title`).
2. Data tables (`.table`, `.table-hover`, `.table-borderless`, responsive wrappers).
3. Metric Stat Badges (`.bg-success-focus`, `.bg-warning-focus`, `.text-primary-600`).
4. Iconography conventions (`iconify-icon` elements with Solar and Radix icon names).
5. Output findings into `/var/www/html/almarmouq3/_documentation/design-language.md`.

---

## 📦 Shared Hosting Architecture Rules

1. **No System Daemons Required**: All background capabilities (such as AI summary generation or invoice calculation) must run via standard PHP requests or database-backed `jobs` table executed via standard HTTP trigger or web cron.
2. **Database Engine**: MySQL 8.0+ / MariaDB standard tables with proper foreign key indexing.
3. **Session & Auth**: Standard Laravel session cookie & sanctum token support.

---

## 🐙 Gitea Repository Genesis

1. Execute Git initialization if needed:
   ```bash
   cd /var/www/html/almarmouq3
   git init
   ```
2. Commit current project structure:
   ```bash
   git add .
   git commit -m "feat(genesis): Al-Marmouq Sovereign CRM & ERP V3 architecture & theme docs"
   ```
3. Set Gitea remote origin (when Gitea service or server is configured) and push tag `v1.0.0-sovereign`.
