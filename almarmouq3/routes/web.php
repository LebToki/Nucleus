<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\CommunicationsController;
use App\Http\Controllers\CrmController;
use App\Http\Controllers\CryptocurrencyController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VenueController;
use App\Services\ProjectAiGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes — 2TI AGENCY Sovereign Autonomous Platform
|--------------------------------------------------------------------------
*/

// Agency Landing Page
Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('index');

// Agency owns coding delivery; media workflows belong to ProductionRoom.
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard');

// Agency Core Index
Route::get('/agency', function () {
    return redirect()->route('command.overview');
})->name('agency.index');

// Projects Matrix (receives live projects from database with avatars & telemetry)
Route::get('/agency/projects', function () {
    if (! Schema::hasTable('projects')) {
        return inertia('Agency/Projects', ['projects' => []]);
    }
    $projects = DB::table('projects')
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($p) {
            $frameworks = json_decode($p->frameworks ?? '[]', true) ?: [];
            $phasesCount = DB::table('phases')->where('project_id', $p->id)->count();
            $phaseIds = DB::table('phases')->where('project_id', $p->id)->pluck('id');
            $tasksCount = DB::table('tasks')->whereIn('phase_id', $phaseIds)->count();
            $taskIds = DB::table('tasks')->whereIn('phase_id', $phaseIds)->pluck('id');
            $miniTasksCount = DB::table('mini_tasks')->whereIn('task_id', $taskIds)->count();
            $billableHours = round(($tasksCount * 0.5) + ($miniTasksCount * 0.15), 1);

            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'path' => $p->path,
                'vhost' => $p->vhost ?: 'http://'.$p->slug.'.local',
                'client_name' => $p->client_name,
                'type' => $p->client_name ? "Client ({$p->client_name})" : 'Internal Node',
                'status' => ucfirst($p->status ?? 'active'),
                'auditScore' => $p->audit_score ?? 96,
                'stack' => implode(' · ', array_slice($frameworks, 0, 3)) ?: 'Custom Stack',
                'image_path' => $p->image_path ?: null,
                'description' => $p->description ?: "Autonomous engineering project for {$p->name} located at {$p->path}.",
                'activeSorties' => $phasesCount,
                'tasks_count' => $tasksCount,
                'mini_tasks_count' => $miniTasksCount,
                'billable_hours' => $billableHours,
                'swarm_time' => "{$billableHours}h",
            ];
        });

    return inertia('Agency/Projects', [
        'projects' => $projects,
    ]);
})->name('agency.projects');

// Canonical /projects Route
Route::get('/projects', fn () => redirect()->route('agency.projects'))->name('projects.index');

// Project Avatar Upload API
Route::post('/api/projects/{id}/avatar', function ($id, Request $request) {
    $project = DB::table('projects')->where('id', $id)->first();
    if (! $project) {
        return response()->json(['success' => false, 'error' => 'Project not found'], 404);
    }

    if ($request->hasFile('avatar')) {
        $file = $request->file('avatar');
        $ext = strtolower($file->getClientOriginalExtension());
        if (! in_array($ext, ['png', 'jpg', 'jpeg', 'svg', 'webp'])) {
            return response()->json(['success' => false, 'error' => 'Invalid file format'], 422);
        }
        $filename = "project_{$id}_".time().".{$ext}";
        $destPath = public_path('uploads/avatars');
        if (! file_exists($destPath)) {
            @mkdir($destPath, 0777, true);
        }
        $file->move($destPath, $filename);
        $avatarPath = "/uploads/avatars/{$filename}";
    } elseif ($request->filled('image_path')) {
        $avatarPath = trim($request->input('image_path'));
    } else {
        return response()->json(['success' => false, 'error' => 'No image provided'], 400);
    }

    DB::table('projects')->where('id', $id)->update([
        'image_path' => $avatarPath,
        'updated_at' => now(),
    ]);

    return response()->json([
        'success' => true,
        'image_path' => $avatarPath,
        'message' => 'Project avatar updated successfully.',
    ]);
})->name('projects.avatar.upload');

// Project Creative Description AI Generation API (Local Mistral)
Route::post('/api/projects/{id}/generate-description', function ($id) {
    $project = DB::table('projects')->where('id', $id)->first();
    if (! $project) {
        return response()->json(['success' => false, 'error' => 'Project not found'], 404);
    }

    $frameworks = json_decode($project->frameworks ?? '[]', true) ?: [];
    $languages = json_decode($project->languages ?? '[]', true) ?: [];
    $desc = ProjectAiGeneratorService::generateDescription($project->name, $frameworks, $languages);

    DB::table('projects')->where('id', $id)->update([
        'description' => $desc,
        'updated_at' => now(),
    ]);

    return response()->json([
        'success' => true,
        'description' => $desc,
        'message' => 'Creative description generated via local Mistral.',
    ]);
})->name('projects.generate.description');

// Project Manifest Docs Generator API (README.md, CHANGELOG.md, VERSION.md)
Route::post('/api/projects/{id}/generate-docs', function ($id) {
    try {
        $results = ProjectAiGeneratorService::writeManifestFiles($id);

        return response()->json([
            'success' => true,
            'files' => $results,
            'message' => 'README.md, CHANGELOG.md, and VERSION.md generated successfully.',
        ]);
    } catch (Throwable $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
})->name('projects.generate.docs');

// Model Bay
Route::get('/agency/models', function () {
    return inertia('Agency/Models');
})->middleware('auth')->name('agency.models');

// Missions & Tasks
Route::get('/agency/missions', function () {
    return view('agency.missions');
})->name('agency.missions');

// Tenants Management (Canonical /tenants)
Route::get('/tenants', function () {
    if (class_exists(NucleusBridgeService::class)) {
        NucleusBridgeService::syncTenantsFromNucleus();
    }

    $tenants = DB::table('tenants')
        ->orderBy('id', 'asc')
        ->get()
        ->map(function ($t) {
            $settings = json_decode($t->settings ?? '{}', true) ?: [];

            return [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
                'email' => $t->email,
                'is_active' => $t->status === 'active',
                'storage_quota' => $t->storage_quota_gb ?? 250,
                'settings' => array_merge($settings, ['storage_quota_gb' => $t->storage_quota_gb ?? 250]),
                'users_count' => DB::table('users')->where('tenant_id', $t->id)->count() ?: 1,
                'projects_count' => DB::table('projects')->where('tenant_id', $t->id)->count(),
                'primary_project_id' => DB::table('projects')->where('tenant_id', $t->id)->orderBy('id', 'asc')->value('id'),
            ];
        });

    return inertia('Agency/Tenants', [
        'tenants' => $tenants,
    ]);
})->name('tenants.index');

// Backward compatibility: /agency/console -> /tenants
Route::get('/agency/console', fn () => redirect()->route('tenants.index'))->name('agency.console');

// Tenant Store Action
Route::post('/agency/tenants', function (Request $request) {
    $name = trim($request->input('name'));
    $email = trim($request->input('email'));
    $quota = intval($request->input('storage_quota') ?: 250);
    if (! empty($name)) {
        DB::table('tenants')->insert([
            'name' => $name,
            'slug' => Str::slug($name),
            'email' => $email ?: Str::slug($name).'@2tinteractive.com',
            'storage_quota_gb' => $quota,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    return redirect()->route('tenants.index');
})->name('agency.tenant.store');

// Tenant Toggle Status Action
Route::post('/agency/tenants/{id}/toggle', function ($id) {
    $tenant = DB::table('tenants')->where('id', $id)->first();
    if ($tenant) {
        $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';
        DB::table('tenants')->where('id', $id)->update(['status' => $newStatus, 'updated_at' => now()]);
    }

    return redirect()->route('tenants.index');
})->name('agency.tenant.toggle');

// Shorthand Base Module Routes (Clean URL routing)
Route::get('/projects', fn () => redirect()->route('agency.projects'))->name('projects.index');
Route::get('/phases', fn () => redirect('/modules/projects/project_view.php?id=1#phases'))->name('phases.index');
Route::get('/patterns', fn () => redirect('/modules/patterns/patterns_view.php'))->name('patterns.index');

// Direct Module Routes
Route::get('/modules/tenants', fn () => redirect()->route('tenants.index'));
Route::get('/modules/projects', fn () => redirect('/modules/projects/project_view.php?id=1'));
Route::get('/modules/patterns', fn () => redirect('/modules/patterns/patterns_view.php'));

// CRM — Clienteling & Sales (Section 3)
Route::middleware('auth')->prefix('crm')->controller(CrmController::class)->group(function (): void {
    // Section A: CRM Cockpit
    Route::get('/cockpit', 'cockpit')->name('crm.cockpit');
    // Section B: VIP Private Clients
    Route::get('/vip-clients', 'vipClients')->name('crm.vip-clients');
    // Section C: Sovereign & B2B Accounts
    Route::get('/accounts', 'accounts')->name('crm.accounts');
    // Section D: B2B Corporate Tenders
    Route::get('/tenders', 'tenders')->name('crm.tenders');
    Route::get('/tenders/{id}', 'tenderShow')->name('crm.tenders.show');
    // Section E: Quote Builder
    Route::get('/quotes', 'quotes')->name('crm.quotes');
    Route::get('/quotes/builder/{id?}', 'quoteBuilder')->name('crm.quotes.builder');
    Route::post('/quotes/store', 'storeQuote')->name('crm.quotes.store');
    Route::post('/quotes/{id}/generate-pdf', 'generateQuotePdf')->name('crm.quotes.pdf');
    // Section F: Customer Dossiers
    Route::get('/dossiers', 'dossiers')->name('crm.dossiers');
    Route::get('/dossiers/{id}', 'dossierShow')->name('crm.dossiers.show');
    // Section G: Sales Ledger
    Route::get('/sales-ledger', 'salesLedger')->name('crm.sales-ledger');
    // Section H: Contract Price Tiers
    Route::get('/price-tiers', 'priceTiers')->name('crm.price-tiers');
    Route::post('/price-tiers', 'storePriceTier')->name('crm.price-tiers.store');
    // Customer Interaction logging
    Route::post('/interactions', 'storeInteraction')->name('crm.interactions.store');
    // Additional CRM routes referenced by sidebar
    Route::get('/customers', 'customers')->name('crm.customers');
    Route::get('/quotes/create', 'quoteBuilder')->name('crm.quotes.create');
    Route::get('/sales', 'salesLedger')->name('crm.sales');
    Route::get('/pricing', 'priceTiers')->name('crm.pricing');
    Route::get('/venues', [VenueController::class, 'index'])->name('venues.index');
});

// --- Module routes referenced by sidebar navigation ---

// Command
Route::middleware('auth')->prefix('command')->controller(CommandController::class)->group(function (): void {
    Route::get('/', 'index')->name('command.overview');
    Route::get('/agenda', 'agenda')->name('command.agenda');
    Route::get('/delegations', 'delegations')->name('command.delegations');
    Route::get('/majlis', 'majlis')->name('command.majlis');
});

// Financial
Route::middleware('auth')->prefix('financial')->controller(FinancialController::class)->group(function (): void {
    Route::get('/', 'index')->name('financial.overview');
    Route::get('/pnl', 'pnl')->name('financial.pnl');
    Route::get('/expenses', 'expenses')->name('financial.expenses');
    Route::get('/tax', 'tax')->name('financial.tax');
});

// Communications
Route::middleware('auth')->prefix('communications')->controller(CommunicationsController::class)->group(function (): void {
    Route::get('/', 'index')->name('communications.cockpit');
    Route::get('/whatsapp', 'whatsapp')->name('communications.whatsapp');
    Route::get('/webmail', 'webmails')->name('communications.webmail');
    Route::get('/majlis-logs', 'majlisLogs')->name('communications.majlis-logs');
    Route::get('/documents', 'documents')->name('communications.documents');
});

// Inventory / Vault Atelier
Route::middleware('auth')->prefix('inventory')->controller(InventoryController::class)->group(function (): void {
    Route::get('/', 'index')->name('inventory.cockpit');
    Route::get('/finished-editions', 'finishedEditions')->name('inventory.finished-editions');
    Route::get('/catalog', 'catalog')->name('inventory.catalog');
    Route::get('/price-history', 'priceHistory')->name('inventory.price-history');
    Route::get('/custom-sets', 'customSets')->name('inventory.custom-sets');
    Route::get('/waiting-list', 'waitingList')->name('inventory.waiting-list');
    Route::get('/coa', 'coa')->name('inventory.coa');
});

// Procurement
Route::middleware('auth')->prefix('procurement')->controller(ProcurementController::class)->group(function (): void {
    Route::get('/', 'index')->name('procurement.cockpit');
    Route::get('/orders', 'orders')->name('procurement.orders');
    Route::get('/inbound', 'inbound')->name('procurement.inbound');
    Route::get('/forecast', 'forecast')->name('procurement.forecast');
});

// Logistics
Route::middleware('auth')->prefix('logistics')->controller(LogisticsController::class)->group(function (): void {
    Route::get('/', 'index')->name('logistics.cockpit');
    Route::get('/white-glove', 'whiteGlove')->name('logistics.white-glove');
    Route::get('/courier', 'courier')->name('logistics.courier');
    Route::get('/showcases', 'showcases')->name('logistics.showcases');
});

// Portal
Route::middleware('auth')->prefix('portal')->controller(PortalController::class)->group(function (): void {
    Route::get('/', 'index')->name('portal.sanctuary');
    Route::get('/allocations', 'allocations')->name('portal.allocations');
    Route::get('/catalog', 'catalog')->name('portal.catalog');
    Route::get('/bespoke-studio', 'bespokeStudio')->name('portal.bespoke-studio');
    Route::get('/orders', 'orders')->name('portal.orders');
    Route::get('/tracking', 'tracking')->name('portal.tracking');
    Route::get('/certificates', 'certificates')->name('portal.certificates');
});

// Administration
Route::middleware('auth')->prefix('admin')->controller(AdminController::class)->group(function (): void {
    Route::get('/', 'index')->name('admin.cockpit');
    Route::get('/users', 'users')->name('admin.users');
    Route::get('/roles', 'roles')->name('admin.roles');
    Route::get('/commercial-rules', 'commercialRules')->name('admin.commercial-rules');
    Route::get('/audit', 'audit')->name('admin.audit');
    Route::get('/settings', 'settings')->name('admin.settings');
});

// --- Shared layout routes (navbar, breadcrumb) ---

// Locale switch
Route::get('/locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'], true)) {
        session(['locale' => $locale]);
    }

    return back();
})->name('locale.switch');

// User profile (navbar "My Profile")
Route::middleware('auth')->get('/profile', [UsersController::class, 'viewProfile'])->name('viewProfile');

// Email / Starred / View Details (navbar + email views)
Route::middleware('auth')->group(function (): void {
    Route::get('/email', [HomeController::class, 'email'])->name('email');
    Route::get('/starred', [HomeController::class, 'starred'])->name('starred');
    Route::get('/view-details', [HomeController::class, 'viewDetails'])->name('viewDetails');
    // Company / Settings (navbar "Setting")
    Route::get('/company', [AdminController::class, 'settings'])->name('company');
});

// --- Authentication ---
Route::get('/signin', [AuthenticationController::class, 'signIn'])->middleware('guest')->name('signin');
Route::get('/login', [AuthenticationController::class, 'signIn'])->middleware('guest')->name('login');
Route::get('/signup', [AuthenticationController::class, 'signUp'])->middleware('guest')->name('signup');
Route::post('/login', [AuthenticationController::class, 'login'])->middleware('guest')->name('login.post');
Route::post('/logout', [AuthenticationController::class, 'logout'])->middleware('auth')->name('logout');
Route::post('/password/email', [AuthenticationController::class, 'sendPasswordResetLink'])->middleware('guest')->name('password.email');

// --- Command sub-action routes (agenda + delegations CRUD) ---
Route::middleware('auth')->prefix('command')->controller(CommandController::class)->group(function (): void {
    Route::post('/agenda', 'storeEvent')->name('command.agenda.store');
    Route::delete('/agenda/{event}', 'destroyEvent')->name('command.agenda.destroy');
    Route::post('/delegations', 'storeDelegation')->name('command.delegations.store');
    Route::delete('/delegations/{delegation}', 'destroyDelegation')->name('command.delegations.destroy');
});

// --- Communications: WhatsApp send + documents show ---
Route::middleware('auth')->prefix('communications')->controller(CommunicationsController::class)->group(function (): void {
    Route::post('/whatsapp/send', 'sendWhatsApp')->name('communications.whatsapp.send');
    Route::get('/documents/{id}', 'documentsShow')->name('communications.documents.show');
});

// --- Portal: certificates download ---
Route::middleware('auth')->get('/portal/certificates/{id}/download', [PortalController::class, 'certificatesDownload'])
    ->name('portal.certificates.download');

// --- Venues CRUD ---
Route::middleware('auth')->prefix('venues')->controller(VenueController::class)->group(function (): void {
    Route::get('/create', 'create')->name('venues.create');
    Route::post('/', 'store')->name('venues.store');
    Route::get('/{venue}', 'show')->name('venues.show');
    Route::get('/{venue}/edit', 'edit')->name('venues.edit');
    Route::put('/{venue}', 'update')->name('venues.update');
    Route::delete('/{venue}', 'destroy')->name('venues.destroy');
    Route::post('/quick-add', 'quickAdd')->name('venues.quick-add');
});

// --- Products CRUD ---
Route::middleware('auth')->prefix('products')->controller(ProductController::class)->group(function (): void {
    Route::get('/', 'catalog')->name('products.catalog');
    Route::get('/create', 'create')->name('products.create');
    Route::post('/', 'store')->name('products.store');
    Route::get('/{product}', 'show')->name('products.show');
    Route::get('/{product}/edit', 'edit')->name('products.edit');
    Route::put('/{product}', 'update')->name('products.update');
    Route::delete('/{product}', 'destroy')->name('products.destroy');
});

// --- Users management ---
Route::middleware('auth')->prefix('users')->controller(UsersController::class)->group(function (): void {
    Route::get('/grid', 'usersGrid')->name('usersGrid');
    Route::get('/list', 'usersList')->name('usersList');
    Route::get('/add', 'addUser')->name('addUser');
});

// --- Password change ---
Route::middleware('auth')->post('/users/change-password', [AuthenticationController::class, 'updatePassword'])
    ->name('users.change-password.update');

// --- Invoice builder ---
Route::middleware('auth')->get('/invoice/add', [InvoiceController::class, 'invoiceAdd'])->name('invoiceAdd');

// --- Forgot password ---
Route::get('/forgot-password', [AuthenticationController::class, 'forgotPassword'])->middleware('guest')->name('forgotPassword');

// --- Chat ---
Route::middleware('auth')->controller(HomeController::class)->group(function (): void {
    Route::get('/chat', 'chatMessage')->name('chatMessage');
    Route::get('/chat/profile', 'chatProfile')->name('chatProfile');
});

// --- Blog ---
Route::middleware('auth')->controller(BlogController::class)->group(function (): void {
    Route::get('/blog', 'blog')->name('blog');
    Route::get('/blog/{id}', 'blogDetails')->name('blogDetails');
});

// --- Cryptocurrency ---
Route::middleware('auth')->controller(CryptocurrencyController::class)->group(function (): void {
    Route::get('/marketplace', 'marketplace')->name('marketplace');
    Route::get('/marketplace/{id}', 'marketplaceDetails')->name('marketplaceDetails');
    Route::get('/portfolio', 'portfolio')->name('portfolio');
    Route::get('/wallet', 'wallet')->name('wallet');
});
