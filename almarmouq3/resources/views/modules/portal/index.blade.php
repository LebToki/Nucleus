@extends('layouts.app')

@section('content')
<div class="d-flex flex-column gap-4">

    {{-- Section Header --}}
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="fw-bold mb-1">Patron Sanctuary</h4>
            <p class="text-neutral-500 text-sm mb-0">Private reserve allocations, active commissions & verified vault provenance</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning-50 text-warning-700 px-3 py-2 radius-8 border border-warning-200">
                <iconify-icon icon="solar:crown-line-duotone" class="me-1 align-middle text-warning-600"></iconify-icon>
                Tier: Sovereign Patron
            </span>
        </div>
    </div>

    {{-- Tier 1: 4 KPI Cards --}}
    <div class="row g-3">
        {{-- Card 1: Private Allocations Held --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #FFF7ED;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-warning-700 text-uppercase">Private Allocations</span>
                        <h3 class="fw-bold text-neutral-900 mt-2 mb-1">3 Editions</h3>
                        <span class="text-xs text-neutral-500">Reserved exclusively for you</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-warning-700 bg-white shadow-xs">
                        <iconify-icon icon="solar:crown-line-duotone" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Vault Certificates Registered --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #ECFDF5;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-teal-700 text-uppercase">Authenticated COAs</span>
                        <h3 class="fw-bold text-neutral-900 mt-2 mb-1">8</h3>
                        <span class="text-xs text-neutral-500">Digital certificates of authenticity</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-teal-700 bg-white shadow-xs">
                        <iconify-icon icon="solar:diploma-verified-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Owned Holdings Value ("What I Own") --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #F0FDF4;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-success-700 text-uppercase">Vault Portfolio Value</span>
                        <h3 class="fw-bold text-success-600 mt-2 mb-1">$124,500.00</h3>
                        <span class="text-xs text-neutral-500">Authenticated flacons & tolas</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-success-600 bg-white shadow-xs">
                        <iconify-icon icon="solar:safe-square-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Active Commissions & En Route ("What Is In Motion") --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #FEF2F2;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-danger-700 text-uppercase">Active Atelier Orders</span>
                        <h3 class="fw-bold text-danger-600 mt-2 mb-1">2 Dispatches</h3>
                        <span class="text-xs text-neutral-500">In bespoke crafting & transit</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-danger-600 bg-white shadow-xs">
                        <iconify-icon icon="solar:routing-2-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bespoke Atelier Callout Banner --}}
    <div class="card border-0 radius-12 p-24" style="background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);">
        <div class="row align-items-center g-3">
            <div class="col-12 col-lg-8">
                <span class="badge bg-warning-500 text-neutral-900 text-xs fw-bold px-2 py-1 mb-2">Haute Sur-Mesure</span>
                <h5 class="text-white fw-bold mb-1">Bespoke Flacon Engraving & Presentation Cases</h5>
                <p class="text-neutral-400 text-sm mb-0">Commission customized crystal flacons with your private monogram, 24k gold leaf calligraphy embossing, or custom calfskin collector trunks.</p>
            </div>
            <div class="col-12 col-lg-4 text-lg-end">
                <a href="{{ route('portal.bespoke-studio') }}" class="btn btn-warning px-20 py-10 radius-8 fw-semibold text-neutral-900">
                    <iconify-icon icon="solar:magic-stick-3-outline" class="me-1 align-middle"></iconify-icon>
                    Enter Atelier Studio
                </a>
            </div>
        </div>
    </div>

    {{-- Tier 2: 2 Visual Telemetry Panels --}}
    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card p-24 radius-12 border-0 shadow-xs h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-semibold mb-0">Private Reserve Portfolio Growth</h6>
                    <span class="text-xs text-neutral-400">Aging Value & Acquisition Value</span>
                </div>
                <div id="chart-patron-holdings" style="min-height: 220px;"></div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card p-24 radius-12 border-0 shadow-xs h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-semibold mb-0">Live Doorstep Consignment Telemetry</h6>
                    <span class="text-xs text-neutral-400">White-Glove Courier Escort</span>
                </div>
                {{-- Live Tracking Mini-Card --}}
                <div class="p-16 radius-8 bg-neutral-50 border border-neutral-200 mt-2">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold text-neutral-900 text-sm">Dispatched Lot #VLT-2026-902</span>
                        <span class="badge bg-primary-50 text-primary-600 text-xs">Out for Hand-off</span>
                    </div>
                    <div class="text-xs text-neutral-500 mb-3">Assigned Diplomatic Escort &bull; Temperature-Controlled Trunk</div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary-600" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-flex justify-content-between text-xs text-neutral-400 mt-2">
                        <span>Vault Dispatched</span>
                        <span>Border Cleared</span>
                        <span class="text-primary-600 fw-semibold">Estimated 14:30 Today</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tier 3: 2 Telemetry Feeds --}}
    <div class="row g-3">
        {{-- Feed 1: Authenticated Flacons & COA Certificates --}}
        <div class="col-12 col-lg-6">
            <div class="card p-20 radius-12 border-0 shadow-xs">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">My Vault Certificates (COA)</h6>
                    <a href="{{ route('portal.certificates') }}" class="text-xs text-primary-600 fw-semibold text-decoration-none">View All 8</a>
                </div>
                <div class="d-flex flex-column gap-2 overflow-y-auto pe-1" style="max-height: 380px;">
                    @forelse ($certificates ?? [] as $coa)
                    <div class="p-12 radius-8 border border-neutral-200 d-flex justify-content-between align-items-center hover-bg-neutral-50 transition-all">
                        <div class="d-flex align-items-center gap-3">
                            <div class="w-36-px h-36-px rounded-circle d-flex align-items-center justify-content-center bg-teal-50 text-teal-700 flex-shrink-0">
                                <iconify-icon icon="solar:diploma-verified-outline" class="text-lg"></iconify-icon>
                            </div>
                            <div>
                                <div class="fw-bold text-neutral-900 text-sm">{{ $coa->edition_title }}</div>
                                <div class="text-xs text-neutral-500 mt-1">COA Serial: {{ $coa->serial_number }} &bull; Harvest: {{ $coa->vintage_year }}</div>
                                <div class="text-xs text-neutral-400 mt-1">Authenticity Sealed: {{ $coa->issued_date }}</div>
                            </div>
                        </div>
                        <a href="{{ route('portal.certificates.download', $coa->id) }}" class="btn btn-sm btn-outline-neutral-300 p-6 radius-6">
                            <iconify-icon icon="solar:download-minimalistic-outline" class="text-base align-middle"></iconify-icon>
                        </a>
                    </div>
                    @empty
                    <div class="p-20 text-center text-neutral-400 text-sm">No certificates registered to this account.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Feed 2: Recent Commissions & Quotations --}}
        <div class="col-12 col-lg-6">
            <div class="card p-20 radius-12 border-0 shadow-xs">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">Recent Commissions & Orders</h6>
                    <a href="{{ route('portal.orders') }}" class="text-xs text-primary-600 fw-semibold text-decoration-none">Order Ledger</a>
                </div>
                <div class="d-flex flex-column gap-2 overflow-y-auto pe-1" style="max-height: 380px;">
                    @forelse ($recentCommissions ?? [] as $order)
                    <div class="p-12 radius-8 border border-neutral-200 d-flex justify-content-between align-items-center hover-bg-neutral-50 transition-all">
                        <div>
                            <div class="fw-bold text-neutral-900 text-sm">{{ $order->order_number }} &bull; {{ $order->package_name }}</div>
                            <div class="text-xs text-neutral-500 mt-1">Customizations: {{ $order->customization_summary }}</div>
                            <div class="text-xs text-neutral-400 mt-1">Ordered: {{ $order->created_at->format('Y-m-d') }}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-neutral-900 text-sm">${{ number_format($order->total_price, 2) }}</div>
                            <span class="badge bg-warning-50 text-warning-700 text-xs mt-1">
                                {{ $order->production_status }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="p-20 text-center text-neutral-400 text-sm">No commissions currently in production.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection