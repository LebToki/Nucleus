<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AiapplicationController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\ComponentpageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommercialController;
use App\Http\Controllers\RoleandaccessController;
use App\Http\Controllers\CryptocurrencyController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CommunicationsController;
use App\Http\Controllers\CommandController;

// -------------------------------------------------------------
// Core / Locale / Authentication
// -------------------------------------------------------------
Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['en', 'ar'], true), 404);
    session(['locale' => $locale]);
    return redirect()->back();
})->name('locale.switch');

// -------------------------------------------------------------
// Authentication Routes
// -------------------------------------------------------------
Route::prefix('authentication')->group(function () {
    Route::controller(AuthenticationController::class)->group(function () {
        Route::get('/signin', 'signin')->name('signin');
        Route::get('/signup', 'signup')->name('signup');
        Route::get('/forgotpassword', 'forgotPassword')->name('forgotPassword');
        Route::get('/login', fn() => redirect()->route('signin'));
        Route::post('/login', 'login')->name('login');
        Route::post('/logout', 'logout')->middleware('auth')->name('logout');
        Route::post('/forgotpassword', 'sendPasswordResetLink')->name('password.email');
    });
});

// -------------------------------------------------------------
// Authenticated Application Modules & Cockpits
// -------------------------------------------------------------
Route::middleware('auth')->group(function () {

    // Default Landing / Root
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/dashboard/section/{section}', [DashboardController::class, 'sectionDashboard'])->name('dashboard.section');

    // -------------------------------------------------------------
    // 1. Executive & Command
    // -------------------------------------------------------------
    Route::prefix('command')->name('command.')->group(function () {
        Route::get('/overview', [CommandController::class, 'index'])->name('overview');
        Route::get('/agenda', [CommandController::class, 'agenda'])->name('agenda');
        Route::get('/agenda/{event}/edit', [CommandController::class, 'editEvent'])->name('agenda.edit');
        Route::post('/agenda', [CommandController::class, 'storeEvent'])->name('agenda.store');
        Route::put('/agenda/{event}', [CommandController::class, 'updateEvent'])->name('agenda.update');
        Route::delete('/agenda/{event}', [CommandController::class, 'destroyEvent'])->name('agenda.destroy');
        Route::get('/delegations', [CommandController::class, 'delegations'])->name('delegations');
        Route::post('/delegations', [CommandController::class, 'storeDelegation'])->name('delegations.store');
        Route::put('/delegations/{delegation}', [CommandController::class, 'updateDelegation'])->name('delegations.update');
        Route::delete('/delegations/{delegation}', [CommandController::class, 'destroyDelegation'])->name('delegations.destroy');
        Route::post('/delegations/{delegation}/status', [CommandController::class, 'updateDelegationStatus'])->name('delegations.status');
        Route::get('/majlis', [CommandController::class, 'majlis'])->name('majlis');
    });

    // -------------------------------------------------------------
    // 2. Financial Telemetry
    // -------------------------------------------------------------
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::view('/overview', 'modules.financial.index')->name('overview');
        Route::view('/pnl', 'modules.financial.pnl')->name('pnl');
        Route::view('/expenses', 'modules.financial.expenses')->name('expenses');
        Route::view('/tax', 'modules.financial.tax')->name('tax');
    });

    // -------------------------------------------------------------
    // 3. Clienteling & Sales
    // -------------------------------------------------------------
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::view('/cockpit', 'modules.crm.index')->name('cockpit');
        Route::view('/vip-clients', 'modules.crm.vip-clients')->name('vip-clients');
        Route::view('/accounts', 'modules.crm.accounts')->name('accounts');
        Route::view('/tenders', 'modules.crm.tenders')->name('tenders');
        Route::view('/quotes/create', 'modules.crm.quotes.create')->name('quotes.create');
        Route::get('/customers', [CommercialController::class, 'customers'])->name('customers');
        Route::get('/sales', [CommercialController::class, 'sales'])->name('sales');
        Route::view('/pricing', 'modules.crm.pricing')->name('pricing');
    });

    // -------------------------------------------------------------
    // 4. Communications Engine
    // -------------------------------------------------------------
    Route::prefix('communications')->name('communications.')->group(function () {
        Route::get('/cockpit', [CommunicationsController::class, 'index'])->name('cockpit');
        Route::get('/whatsapp', [CommunicationsController::class, 'whatsapp'])->name('whatsapp');
        Route::post('/whatsapp/send', [CommunicationsController::class, 'sendWhatsApp'])->name('whatsapp.send');
        Route::get('/webmail', [CommunicationsController::class, 'webmails'])->name('webmail');
        Route::get('/majlis-logs', [CommunicationsController::class, 'majlisLogs'])->name('majlis-logs');
        Route::get('/documents', [CommunicationsController::class, 'documents'])->name('documents');
    });

    // -------------------------------------------------------------
    // 5. Haute Parfumerie & Vault (Inventory)
    // -------------------------------------------------------------
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::view('/cockpit', 'modules.vault_atelier.index')->name('cockpit');
        Route::view('/finished-editions', 'modules.vault_atelier.finished-editions')->name('finished-editions');
        Route::get('/catalog', [ProductController::class, 'catalog'])->name('catalog');
        Route::view('/price-history', 'modules.vault_atelier.price-history')->name('price-history');
        Route::view('/custom-sets', 'modules.vault_atelier.custom-sets')->name('custom-sets');
        Route::view('/waiting-list', 'modules.vault_atelier.waiting-list')->name('waiting-list');
        Route::view('/coa', 'modules.vault_atelier.coa')->name('coa');
    });

    // -------------------------------------------------------------
    // 6. Procurement & Sourcing
    // -------------------------------------------------------------
    Route::prefix('procurement')->name('procurement.')->group(function () {
        Route::view('/cockpit', 'modules.procurement.index')->name('cockpit');
        Route::view('/orders', 'modules.procurement.orders')->name('orders');
        Route::view('/inbound', 'modules.procurement.inbound')->name('inbound');
        Route::view('/forecast', 'modules.procurement.forecast')->name('forecast');
    });

    // -------------------------------------------------------------
    // 7. Logistics & Doorstep Fulfillment
    // -------------------------------------------------------------
    Route::prefix('logistics')->name('logistics.')->group(function () {
        Route::view('/cockpit', 'modules.logistics.index')->name('cockpit');
        Route::view('/white-glove', 'modules.logistics.white-glove')->name('white-glove');
        Route::view('/courier', 'modules.logistics.courier')->name('courier');
        Route::view('/showcases', 'modules.logistics.showcases')->name('showcases');
    });

    // -------------------------------------------------------------
    // 8. Client Sanctuary (Portal)
    // -------------------------------------------------------------
    Route::prefix('portal')->name('portal.')->group(function () {
        Route::view('/sanctuary', 'modules.portal.index')->name('sanctuary');
        Route::view('/allocations', 'modules.portal.allocations')->name('allocations');
        Route::view('/catalog', 'modules.portal.catalog')->name('catalog');
        Route::view('/bespoke-studio', 'modules.portal.bespoke-studio')->name('bespoke-studio');
        Route::view('/orders', 'modules.portal.orders')->name('orders');
        Route::view('/tracking', 'modules.portal.tracking')->name('tracking');
        Route::view('/certificates', 'modules.portal.certificates')->name('certificates');
    });

    // -------------------------------------------------------------
    // 9. System Administration
    // -------------------------------------------------------------
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::view('/cockpit', 'modules.administration.index')->name('cockpit');
        Route::view('/users', 'modules.administration.users')->name('users');
        Route::view('/roles', 'modules.administration.roles')->name('roles');
        Route::view('/commercial-rules', 'modules.administration.commercial-rules')->name('commercial-rules');
        Route::view('/audit', 'modules.administration.audit')->name('audit');
        Route::view('/settings', 'modules.administration.settings')->name('settings');
    });

    // -------------------------------------------------------------
    // Product Resource CRUD & Commercial Actions
    // -------------------------------------------------------------
    Route::resource('products', ProductController::class)->except(['index']);
    Route::get('products/catalog', [ProductController::class, 'catalog'])->name('products.catalog');
    Route::get('products/price-history', [ProductController::class, 'priceHistory'])->name('products.price-history');
    Route::get('commercial/customers', [CommercialController::class, 'customers'])->name('commercial.customers');
    Route::get('commercial/sales', [CommercialController::class, 'sales'])->name('commercial.sales');
    Route::get('quotes/create', [InvoiceController::class, 'quoteBuilder'])->name('quotes.create');

    // -------------------------------------------------------------
    // Users / Profile Settings
    // -------------------------------------------------------------
    Route::prefix('modules/users')->group(function () {
        Route::get('/changepass', [AuthenticationController::class, 'changePassword'])->name('users.change-password');
        Route::post('/changepass', [AuthenticationController::class, 'updatePassword'])->name('users.change-password.update');
    });
});

// -------------------------------------------------------------
// Legacy Theme Component & Demo Routes (Maintained for reference, to be removed in-time)
// -------------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::controller(HomeController::class)->group(function () {
        Route::get('calendar', 'calendar')->name('calendar');
        Route::get('chatmessage', 'chatMessage')->name('chatMessage');
        Route::get('chatempty', 'chatempty')->name('chatempty');
        Route::get('email', 'email')->name('email');
        Route::get('error', 'error1')->name('error');
        Route::get('faq', 'faq')->name('faq');
        Route::get('gallery', 'gallery')->name('gallery');
        Route::get('kanban', 'kanban')->name('kanban');
        Route::get('pricing', 'pricing')->name('pricing');
        Route::get('termscondition', 'termsCondition')->name('termsCondition');
        Route::get('widgets', 'widgets')->name('widgets');
        Route::get('chatprofile', 'chatProfile')->name('chatProfile');
        Route::get('veiwdetails', 'veiwDetails')->name('veiwDetails');
        Route::get('blankPage', 'blankPage')->name('blankPage');
        Route::get('comingSoon', 'comingSoon')->name('comingSoon');
        Route::get('maintenance', 'maintenance')->name('maintenance');
        Route::get('starred', 'starred')->name('starred');
        Route::get('testimonials', 'testimonials')->name('testimonials');
    });

    Route::prefix('dashboard')->controller(DashboardController::class)->group(function () {
        Route::get('/index', 'index')->name('dashboard.index');
        Route::get('/index2', 'index2')->name('index2');
        Route::get('/index3', 'index3')->name('index3');
        Route::get('/index4', 'index4')->name('index4');
        Route::get('/index5', 'index5')->name('index5');
        Route::get('/index6', 'index6')->name('index6');
        Route::get('/index7', 'index7')->name('index7');
        Route::get('/index8', 'index8')->name('index8');
        Route::get('/index9', 'index9')->name('index9');
        Route::get('/index10', 'index10')->name('index10');
        Route::get('/wallet', 'wallet')->name('wallet');
    });

    Route::prefix('forms')->controller(FormsController::class)->group(function () {
        Route::get('/form-layout', 'formLayout')->name('formLayout');
        Route::get('/form-validation', 'formValidation')->name('formValidation');
        Route::get('/form', 'form')->name('form');
        Route::get('/wizard', 'wizard')->name('wizard');
    });

    Route::prefix('invoice')->controller(InvoiceController::class)->group(function () {
        Route::get('/invoice-add', 'invoiceAdd')->name('invoiceAdd');
        Route::get('/invoice-edit', 'invoiceEdit')->name('invoiceEdit');
        Route::get('/invoice-list', 'invoiceList')->name('invoiceList');
        Route::get('/invoice-preview', 'invoicePreview')->name('invoicePreview');
    });

    Route::prefix('settings')->controller(SettingsController::class)->group(function () {
        Route::get('/company', 'company')->name('company');
        Route::get('/currencies', 'currencies')->name('currencies');
        Route::get('/language', 'language')->name('language');
        Route::get('/notification', 'notification')->name('notification');
        Route::get('/notification-alert', 'notificationAlert')->name('notificationAlert');
        Route::get('/payment-gateway', 'paymentGateway')->name('paymentGateway');
        Route::get('/theme', 'theme')->name('theme');
    });

    Route::prefix('table')->controller(TableController::class)->group(function () {
        Route::get('/tablebasic', 'tableBasic')->name('tableBasic');
        Route::get('/tabledata', 'tableData')->name('tableData');
    });

    Route::prefix('users')->controller(UsersController::class)->group(function () {
        Route::get('/add-user', 'addUser')->name('addUser');
        Route::get('/users-grid', 'usersGrid')->name('usersGrid');
        Route::get('/users-list', 'usersList')->name('usersList');
        Route::get('/view-profile', 'viewProfile')->name('viewProfile');
    });

    Route::prefix('roleandaccess')->controller(RoleandaccessController::class)->group(function () {
        Route::get('/assignRole', 'assignRole')->name('assignRole');
        Route::get('/roleAaccess', 'roleAaccess')->name('roleAaccess');
    });

    Route::prefix('chart')->controller(ChartController::class)->group(function () {
        Route::get('/columnchart', 'columnChart')->name('columnChart');
        Route::get('/linechart', 'lineChart')->name('lineChart');
        Route::get('/piechart', 'pieChart')->name('pieChart');
    });

    Route::prefix('blog')->controller(BlogController::class)->group(function () {
        Route::get('/addBlog', 'addBlog')->name('addBlog');
        Route::get('/blog', 'blog')->name('blog');
        Route::get('/blogDetails', 'blogDetails')->name('blogDetails');
    });

    Route::prefix('cryptocurrency')->controller(CryptocurrencyController::class)->group(function () {
        Route::get('/marketplace', 'marketplace')->name('marketplace');
        Route::get('/marketplacedetails', 'marketplaceDetails')->name('marketplaceDetails');
        Route::get('/portfolio', 'portfolio')->name('portfolio');
        Route::get('/wallet', 'wallet')->name('wallet');
    });

    Route::prefix('aiapplication')->controller(AiapplicationController::class)->group(function () {
        Route::get('/codegenerator', 'codeGenerator')->name('codeGenerator');
        Route::get('/codegeneratornew', 'codeGeneratorNew')->name('codeGeneratorNew');
        Route::get('/imagegenerator', 'imageGenerator')->name('imageGenerator');
        Route::get('/textgeneratornew', 'textGeneratorNew')->name('textGeneratorNew');
        Route::get('/textgenerator', 'textGenerator')->name('textGenerator');
        Route::get('/videogenerator', 'videoGenerator')->name('videoGenerator');
        Route::get('/voicegenerator', 'voiceGenerator')->name('voiceGenerator');
    });

    Route::prefix('componentspage')->controller(ComponentpageController::class)->group(function () {
        Route::get('/alert', 'alert')->name('alert');
        Route::get('/avatar', 'avatar')->name('avatar');
        Route::get('/badges', 'badges')->name('badges');
        Route::get('/button', 'button')->name('button');
        Route::get('/calendar', 'calendar')->name('calendar');
        Route::get('/card', 'card')->name('card');
        Route::get('/carousel', 'carousel')->name('carousel');
        Route::get('/colors', 'colors')->name('colors');
        Route::get('/dropdown', 'dropdown')->name('dropdown');
        Route::get('/imageupload', 'imageUpload')->name('imageUpload');
        Route::get('/list', 'list')->name('list');
        Route::get('/pagination', 'pagination')->name('pagination');
        Route::get('/progress', 'progress')->name('progress');
        Route::get('/radio', 'radio')->name('radio');
        Route::get('/starrating', 'starRating')->name('starRating');
        Route::get('/switch', 'switch')->name('switch');
        Route::get('/tabs', 'tabs')->name('tabs');
        Route::get('/tags', 'tags')->name('tags');
        Route::get('/tooltip', 'tooltip')->name('tooltip');
        Route::get('/typography', 'typography')->name('typography');
        Route::get('/videos', 'videos')->name('videos');
    });
});
