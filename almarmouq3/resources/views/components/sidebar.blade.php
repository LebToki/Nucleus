@php
    $user = auth()->user();

    $navSections = [
        // Command section is visible to users with 'owner', 'assistant', or 'curator' roles
        [
            'group' => __('entities.modules.command'),
            'roles' => ['owner', 'assistant', 'curator'],
            'items' => [
                [
                    'title' => __('entities.sidebar.dashboard_overview'),
                    'route' => 'command.overview',
                    'icon' => 'solar:home-smile-angle-outline',
                ],
                [
                    'title' => __('entities.sidebar.daily_agenda'),
                    'route' => 'command.agenda',
                    'icon' => 'solar:calendar-date-outline',
                ],
                [
                    'title' => __('entities.sidebar.delegations'),
                    'route' => 'command.delegations',
                    'icon' => 'solar:checklist-minimalistic-outline',
                ],
                [
                    'title' => __('entities.sidebar.majlis_calendar'),
                    'route' => 'command.majlis',
                    'icon' => 'solar:users-group-two-rounded-outline',
                ],
            ],
        ],
        // Financial section is only visible to users with the 'owner' role
        [
            'group' => __('entities.modules.financial'),
            'roles' => ['owner'],
            'items' => [
                [
                    'title' => __('entities.sidebar.financial_overview'),
                    'route' => 'financial.overview', // Dedicated cockpit
                    'icon' => 'solar:chart-square-outline',
                ],
                [
                    'title' => __('entities.sidebar.pnl'),
                    'route' => 'financial.pnl',
                    'icon' => 'hugeicons:invoice-03',
                ],
                [
                    'title' => __('entities.sidebar.cogs_expenses'),
                    'route' => 'financial.expenses',
                    'icon' => 'solar:card-transfer-outline',
                ],
                [
                    'title' => __('entities.sidebar.tax_settlements'),
                    'route' => 'financial.tax',
                    'icon' => 'solar:document-text-outline',
                ],
            ],
        ],
        // CRM section is visible to users with 'owner' or 'curator' roles
        [
            'group' => __('entities.modules.crm'),
            'roles' => ['owner', 'curator'],
            'items' => [
                [
                    'title' => __('entities.sidebar.crm_cockpit'),
                    'route' => 'crm.cockpit',
                    'icon' => 'solar:chart-2-bold-duotone',
                ],
                [
                    'title' => __('entities.sidebar.vip_clients'),
                    'route' => 'crm.vip-clients',
                    'icon' => 'solar:crown-line-duotone',
                ],
                [
                    'title' => __('entities.sidebar.accounts'),
                    'route' => 'crm.accounts',
                    'icon' => 'solar:user-id-outline',
                ],
                [
                    'title' => __('entities.sidebar.tenders'),
                    'route' => 'crm.tenders',
                    'icon' => 'solar:document-add-outline',
                ],
                [
                    'title' => __('entities.sidebar.quote_builder'),
                    'route' => 'crm.quotes.create',
                    'icon' => 'solar:bill-list-outline',
                ],
                [
                    'title' => __('entities.sidebar.customers'),
                    'route' => 'crm.customers',
                    'icon' => 'flowbite:users-group-outline',
                ],
                [
                    'title' => __('entities.sidebar.sales_ledger'),
                    'route' => 'crm.sales',
                    'icon' => 'solar:wallet-money-outline',
                ],
                [
                    'title' => __('entities.sidebar.price_tiers'),
                    'route' => 'crm.pricing',
                    'icon' => 'solar:tag-price-outline',
                ],
            ],
        ],
        // Communications section is visible to users with 'owner', 'assistant', or 'curator' roles
        [
            'group' => __('entities.modules.communications'),
            'roles' => ['owner', 'assistant', 'curator'],
            'items' => [
                [
                    'title' => __('entities.sidebar.comm_cockpit'),
                    'route' => 'communications.cockpit',
                    'icon' => 'solar:chat-round-call-outline',
                ],
                [
                    'title' => __('entities.sidebar.whatsapp'),
                    'route' => 'communications.whatsapp',
                    'icon' => 'solar:chat-round-dots-outline',
                ],
                [
                    'title' => __('entities.sidebar.webmail'),
                    'route' => 'communications.webmail',
                    'icon' => 'mage:email',
                ],
                [
                    'title' => __('entities.sidebar.majlis_logs'),
                    'route' => 'communications.majlis-logs',
                    'icon' => 'bi:chat-dots',
                ],
                [
                    'title' => __('entities.sidebar.client_vaults'),
                    'route' => 'communications.documents',
                    'icon' => 'solar:folder-with-files-outline',
                ],
            ],
        ],
        // Vault section is visible to users with 'owner' or 'curator' roles
        [
            'group' => __('entities.modules.vault_atelier'), // e.g., "Haute Parfumerie & Vault"
            'roles' => ['owner', 'curator'],
            'items' => [
                [
                    'title' => __('entities.sidebar.vault_telemetry'),
                    'route' => 'inventory.cockpit',
                    'icon' => 'solar:box-minimalistic-outline',
                ],
                [
                    'title' => __('entities.sidebar.finished_sets'),
                    'route' => 'inventory.finished-editions',
                    'icon' => 'solar:bottle-outline',
                ],
                [
                    'title' => __('entities.sidebar.product_catalog'),
                    'route' => 'inventory.catalog',
                    'icon' => 'solar:folder-with-files-outline',
                ],
                [
                    'title' => __('entities.sidebar.price_history'),
                    'route' => 'inventory.price-history',
                    'icon' => 'solar:history-outline',
                ],
                [
                    'title' => __('entities.sidebar.bespoke_assembly'),
                    'route' => 'inventory.custom-sets',
                    'icon' => 'solar:layers-minimalistic-outline',
                ],
                [
                    'title' => __('entities.sidebar.vip_waiting_list'),
                    'route' => 'inventory.waiting-list',
                    'icon' => 'solar:clock-circle-outline',
                ],
                [
                    'title' => __('entities.sidebar.authenticity_provenance'),
                    'route' => 'inventory.coa',
                    'icon' => 'solar:diploma-verified-outline',
                ],
            ],
        ],
        // Procurement section is visible to users with 'owner' or 'distributor' roles
        [
            'group' => __('entities.modules.procurement'), // "Procurement & Sourcing"
            'roles' => ['owner', 'distributor'],
            'items' => [
                [
                    'title' => __('entities.sidebar.procurement_cockpit'),
                    'route' => 'procurement.cockpit',
                    'icon' => 'solar:cart-large-minimalistic-outline',
                ],
                [
                    'title' => __('entities.sidebar.supplier_orders'),
                    'route' => 'procurement.orders',
                    'icon' => 'solar:document-add-outline',
                ],
                [
                    'title' => __('entities.sidebar.inbound_shipments'),
                    'route' => 'procurement.inbound',
                    'icon' => 'solar:inbox-in-outline',
                ],
                [
                    'title' => __('entities.sidebar.replenishment_forecast'),
                    'route' => 'procurement.forecast',
                    'icon' => 'solar:graph-up-outline',
                ],
            ],
        ],
        // Logistics section is visible to users with 'owner', 'assistant', or 'curator' roles
        [
            'group' => __('entities.modules.logistics'),
            'roles' => ['owner', 'assistant', 'curator'],
            'items' => [
                [
                    'title' => __('entities.sidebar.logistics_cockpit'),
                    'route' => 'logistics.cockpit',
                    'icon' => 'solar:delivery-outline',
                ],
                [
                    'title' => __('entities.sidebar.white_glove'),
                    'route' => 'logistics.white-glove',
                    'icon' => 'solar:star-fall-minimalistic-outline',
                ],
                [
                    'title' => __('entities.sidebar.courier'),
                    'route' => 'logistics.courier',
                    'icon' => 'solar:routing-2-outline',
                ],
                [
                    'title' => __('entities.sidebar.diplomatic_showcases'),
                    'route' => 'logistics.showcases',
                    'icon' => 'solar:gallery-wide-linear',
                ],
            ],
        ],
        // Portal section is visible to users with the 'client' role
        [
            'group' => __('entities.modules.portal'), // "Client Sanctuary"
            'roles' => ['client'],
            'items' => [
                [
                    'title' => __('entities.sidebar.sanctuary_overview'),
                    'route' => 'portal.sanctuary',
                    'icon' => 'solar:shield-user-outline',
                ],
                [
                    'title' => __('entities.sidebar.private_allocations'),
                    'route' => 'portal.allocations',
                    'icon' => 'solar:crown-line-duotone',
                ],
                [
                    'title' => __('entities.sidebar.reserve_catalog'),
                    'route' => 'portal.catalog',
                    'icon' => 'solar:bottle-outline',
                ],
                [
                    'title' => __('entities.sidebar.bespoke_studio'), // Packaging, engraving, embossing
                    'route' => 'portal.bespoke-studio',
                    'icon' => 'solar:magic-stick-3-outline',
                ],
                [
                    'title' => __('entities.sidebar.orders_quotes'),
                    'route' => 'portal.orders',
                    'icon' => 'solar:receipt-item-outline',
                ],
                [
                    'title' => __('entities.sidebar.track_delivery'),
                    'route' => 'portal.tracking',
                    'icon' => 'solar:map-point-wave-outline',
                ],
                [
                    'title' => __('entities.sidebar.vault_certificates'),
                    'route' => 'portal.certificates',
                    'icon' => 'solar:diploma-verified-outline',
                ],
            ],
        ],
        // Administration section is only visible to users with the 'owner' role
        [
            'group' => __('entities.modules.administration'),
            'roles' => ['owner'],
            'items' => [
                [
                    'title' => __('entities.sidebar.admin_cockpit'),
                    'route' => 'admin.cockpit',
                    'icon' => 'solar:shield-check-outline',
                ],
                [
                    'title' => __('entities.sidebar.team_users'),
                    'route' => 'admin.users',
                    'icon' => 'solar:users-group-rounded-outline',
                ],
                [
                    'title' => __('entities.sidebar.rbac_permissions'),
                    'route' => 'admin.roles',
                    'icon' => 'solar:lock-keyhole-minimalistic-outline',
                ],
                [
                    'title' => __('entities.sidebar.commercial_rules'), // Margins, Tier discounts, Commission caps
                    'route' => 'admin.commercial-rules',
                    'icon' => 'solar:tag-price-outline',
                ],
                [
                    'title' => __('entities.sidebar.audit_trail'),
                    'route' => 'admin.audit',
                    'icon' => 'solar:history-outline',
                ],
                [
                    'title' => __('entities.sidebar.boutique_mail_settings'),
                    'route' => 'admin.settings',
                    'icon' => 'solar:settings-minimalistic-outline',
                ],
            ],
        ],
    ];
@endphp

<aside class="sidebar">
    <button type="button" class="sidebar-close-btn">
        <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
    </button>
    <div>
        <a href="{{ route('index') }}" class="sidebar-logo">
            <img src="{{ asset('assets/images/logo.png') }}" alt="{{ __('entities.app_name') }}" class="light-logo">
            <img src="{{ asset('assets/images/logo-light.png') }}" alt="{{ __('entities.app_name') }}" class="dark-logo">
            <img src="{{ asset('assets/images/logo-icon.png') }}" alt="{{ __('entities.app_name') }}" class="logo-icon">
        </a>
    </div>
    <div class="sidebar-menu-area">
        <ul class="sidebar-menu" id="sidebar-menu">
            @foreach ($navSections as $section)
                @php
                    $isAllowed = empty($section['roles']) || ($user && $user->hasRole($section['roles']));
                @endphp

                @if ($isAllowed && !empty($section['items']))
                    <li class="sidebar-menu-group-title">{{ $section['group'] }}</li>

                    @foreach ($section['items'] as $item)
                        @php
                            $isActive = request()->routeIs($item['route']);
                        @endphp
                        <li class="{{ $isActive ? 'active-page' : '' }}">
                            <a href="{{ route($item['route']) }}">
                                <iconify-icon icon="{{ $item['icon'] }}" class="menu-icon"></iconify-icon>
                                <span>{{ $item['title'] }}</span>
                            </a>
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ul>
    </div>
</aside>
