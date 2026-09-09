@extends('layouts.app')

@section('content')
<div class="d-flex flex-column gap-4">

    {{-- Section Header --}}
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="fw-bold mb-1">Reserve Vault & Atelier Cockpit</h4>
            <p class="text-neutral-500 text-sm mb-0">Rare oils, tolas, bespoke assemble-to-order sets & certificate verification</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-neutral-200 text-neutral-800 px-3 py-2 radius-8">
                <iconify-icon icon="solar:safe-square-outline" class="me-1 align-middle text-warning-600"></iconify-icon>
                Vault Status: Secured
            </span>
        </div>
    </div>

    {{-- Tier 1: 4 KPI Cards --}}
    <div class="row g-3">
        {{-- Card 1: Unique Distillations / Catalog SKUs --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #FFF7ED;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-warning-700 text-uppercase">Curation Catalog</span>
                        <h3 class="fw-bold text-neutral-900 mt-2 mb-1">48</h3>
                        <span class="text-xs text-neutral-500">Active oils, tolas & sets</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-warning-700 bg-white shadow-xs">
                        <iconify-icon icon="solar:bottle-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Certified Provenance (COAs) --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #ECFDF5;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-teal-700 text-uppercase">Authenticated Batches</span>
                        <h3 class="fw-bold text-neutral-900 mt-2 mb-1">112</h3>
                        <span class="text-xs text-neutral-500">Issued Certificates of Authenticity</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-teal-700 bg-white shadow-xs">
                        <iconify-icon icon="solar:diploma-verified-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Ready Vault Valuation ("What We Got") --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #F0FDF4;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-success-700 text-uppercase">Total Vault Valuation</span>
                        <h3 class="fw-bold text-success-600 mt-2 mb-1">$1,840,500.00</h3>
                        <span class="text-xs text-neutral-500">Unreserved physical stock</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-success-600 bg-white shadow-xs">
                        <iconify-icon icon="solar:box-minimalistic-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: VIP Waiting List & Allocations ("What We Owe / Committed") --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #FEF2F2;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-danger-700 text-uppercase">Pending Allocations</span>
                        <h3 class="fw-bold text-danger-600 mt-2 mb-1">19 Sets</h3>
                        <span class="text-xs text-neutral-500">Unfulfilled VIP queue demand</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-danger-600 bg-white shadow-xs">
                        <iconify-icon icon="solar:clock-circle-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tier 2: 2 Velocity Curves --}}
    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card p-24 radius-12 border-0 shadow-xs h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-semibold mb-0">Vault Depletion vs Inflow Rate</h6>
                    <span class="text-xs text-neutral-400">Bottled Stock vs Inbound Aging Distillations</span>
                </div>
                <div id="chart-vault-depletion" style="min-height: 220px;"></div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card p-24 radius-12 border-0 shadow-xs h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-semibold mb-0">Vintage Appreciation Index</h6>
                    <span class="text-xs text-neutral-400">Average Historical Price Evolution</span>
                </div>
                <div id="chart-price-appreciation" style="min-height: 220px;"></div>
            </div>
        </div>
    </div>

    {{-- Tier 3: 2 Telemetry Feeds --}}
    <div class="row g-3">
        {{-- Feed 1: Recent Vault Movements & Assemblies --}}
        <div class="col-12 col-lg-6">
            <div class="card p-20 radius-12 border-0 shadow-xs">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">Recent Vault Dispatches & Assemblies</h6>
                    <span class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">Last 5 Days</span>
                </div>
                <div class="d-flex flex-column gap-2 overflow-y-auto pe-1" style="max-height: 380px;">
                    @forelse ($recentVaultEvents ?? [] as $event)
                    <div class="p-12 radius-8 border border-neutral-200 d-flex justify-content-between align-items-center hover-bg-neutral-50 transition-all">
                        <div>
                            <div class="fw-bold text-neutral-900 text-sm">{{ $event->edition_name }}</div>
                            <div class="text-xs text-neutral-500 mt-1">Lot: {{ $event->lot_number }} &bull; {{ $event->quantity_units }} units dispatched</div>
                            <div class="text-xs text-neutral-400 mt-1">{{ $event->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <span class="badge bg-success-50 text-success-600 text-xs px-2 py-1">
                            Dispatched
                        </span>
                    </div>
                    @empty
                    <div class="p-20 text-center text-neutral-400 text-sm">No recent vault dispatches.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Feed 2: High Priority VIP Waiting Queue --}}
        <div class="col-12 col-lg-6">
            <div class="card p-20 radius-12 border-0 shadow-xs">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">Critical VIP Waiting Queue</h6>
                    <span class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">Awaiting Allocation</span>
                </div>
                <div class="d-flex flex-column gap-2 overflow-y-auto pe-1" style="max-height: 380px;">
                    @forelse ($vipQueue ?? [] as $waiter)
                    <div class="p-12 radius-8 border border-neutral-200 d-flex justify-content-between align-items-center hover-bg-neutral-50 transition-all">
                        <div>
                            <div class="fw-bold text-neutral-900 text-sm">{{ $waiter->client_name }}</div>
                            <div class="text-xs text-neutral-500 mt-1">Requesting: {{ $waiter->requested_edition }} ({{ $waiter->volume_tolas }} Tolas)</div>
                            <div class="text-xs text-neutral-400 mt-1">Queued on: {{ $waiter->created_at->format('Y-m-d') }}</div>
                        </div>
                        <span class="badge bg-warning-50 text-warning-700 text-xs px-2 py-1">
                            Priority {{ $waiter->priority_level }}
                        </span>
                    </div>
                    @empty
                    <div class="p-20 text-center text-neutral-400 text-sm">No active backorders in the queue.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection