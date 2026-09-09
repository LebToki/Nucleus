@extends('layouts.app')

@section('content')
<div class="d-flex flex-column gap-4">

    {{-- Section Header --}}
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="fw-bold mb-1">Logistics & Doorstep Fulfillment Cockpit</h4>
            <p class="text-neutral-500 text-sm mb-0">Secure white-glove escorts, insured couriers & diplomatic showcase transit telemetry</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-neutral-200 text-neutral-800 px-3 py-2 radius-8">
                <iconify-icon icon="solar:shield-keyhole-minimalistic-outline" class="me-1 align-middle text-primary-600"></iconify-icon>
                Chain of Custody Active
            </span>
        </div>
    </div>

    {{-- Tier 1: 4 KPI Cards --}}
    <div class="row g-3">
        {{-- Card 1: Active In-Transit Missions --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #FFF7ED;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-warning-700 text-uppercase">Active En Route</span>
                        <h3 class="fw-bold text-neutral-900 mt-2 mb-1">8</h3>
                        <span class="text-xs text-neutral-500">Live courier & escort dispatches</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-warning-700 bg-white shadow-xs">
                        <iconify-icon icon="solar:routing-2-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Diplomatic & Showcase Trunks --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #ECFDF5;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-teal-700 text-uppercase">Deployed Showcases</span>
                        <h3 class="fw-bold text-neutral-900 mt-2 mb-1">3</h3>
                        <span class="text-xs text-neutral-500">Private trunk presentations</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-teal-700 bg-white shadow-xs">
                        <iconify-icon icon="solar:suitcase-tag-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Cleared Hand Deliveries ("What Got Delivered / Signed") --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #F0FDF4;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-success-700 text-uppercase">Cleared Deliveries</span>
                        <h3 class="fw-bold text-success-600 mt-2 mb-1">54</h3>
                        <span class="text-xs text-neutral-500">100% verified PODs this month</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-success-600 bg-white shadow-xs">
                        <iconify-icon icon="solar:check-circle-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: High-Value Transit Capital ("What is Pending / In Transit") --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #FEF2F2;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-danger-700 text-uppercase">Consignments In Motion</span>
                        <h3 class="fw-bold text-danger-600 mt-2 mb-1">$312,000.00</h3>
                        <span class="text-xs text-neutral-500">Insured valuation currently in transit</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-danger-600 bg-white shadow-xs">
                        <iconify-icon icon="solar:shield-warning-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tier 2: 2 Trend Graphs --}}
    <div class="row g-3">
        <div class="col-12 col-lg-6">
            <div class="card p-24 radius-12 border-0 shadow-xs h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-semibold mb-0">Delivery Method Distribution</h6>
                    <span class="text-xs text-neutral-400">White-Glove Escort vs Secure Air Express</span>
                </div>
                <div id="chart-fulfillment-velocity" style="min-height: 220px;"></div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card p-24 radius-12 border-0 shadow-xs h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-semibold mb-0">Regional Hand-off Transit Times</h6>
                    <span class="text-xs text-neutral-400">Dispatch to Handshake Duration</span>
                </div>
                <div id="chart-transit-duration" style="min-height: 220px;"></div>
            </div>
        </div>
    </div>

    {{-- Tier 3: 2 Telemetry Feeds --}}
    <div class="row g-3">
        {{-- Feed 1: Active White-Glove Hand-offs --}}
        <div class="col-12 col-lg-6">
            <div class="card p-20 radius-12 border-0 shadow-xs">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">Active White-Glove Hand-offs</h6>
                    <span class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">Direct Escort</span>
                </div>
                <div class="d-flex flex-column gap-2 overflow-y-auto pe-1" style="max-height: 380px;">
                    @forelse ($whiteGloveDispatches ?? [] as $dispatch)
                    <div class="p-12 radius-8 border border-neutral-200 d-flex justify-content-between align-items-center hover-bg-neutral-50 transition-all">
                        <div>
                            <div class="fw-bold text-neutral-900 text-sm">{{ $dispatch->consignment_code }} &bull; {{ $dispatch->client_name }}</div>
                            <div class="text-xs text-neutral-500 mt-1">Escort: {{ $dispatch->officer_name }} &bull; Dest: {{ $dispatch->destination_protocol }}</div>
                            <div class="text-xs text-neutral-400 mt-1">ETA: {{ $dispatch->scheduled_slot }}</div>
                        </div>
                        <span class="badge bg-warning-50 text-warning-700 text-xs px-2 py-1">
                            {{ $dispatch->transit_status }}
                        </span>
                    </div>
                    @empty
                    <div class="p-20 text-center text-neutral-400 text-sm">No active white-glove missions right now.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Feed 2: Diplomatic Showcases & Trunks --}}
        <div class="col-12 col-lg-6">
            <div class="card p-20 radius-12 border-0 shadow-xs">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">Diplomatic Showcases & Trunks</h6>
                    <span class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">Curated Displays</span>
                </div>
                <div class="d-flex flex-column gap-2 overflow-y-auto pe-1" style="max-height: 380px;">
                    @forelse ($showcaseDeployments ?? [] as $showcase)
                    <div class="p-12 radius-8 border border-neutral-200 d-flex justify-content-between align-items-center hover-bg-neutral-50 transition-all">
                        <div>
                            <div class="fw-bold text-neutral-900 text-sm">{{ $showcase->trunk_id }} &bull; {{ $showcase->host_venue }}</div>
                            <div class="text-xs text-neutral-500 mt-1">{{ $showcase->curation_theme }} &bull; {{ $showcase->insured_flacons_count }} Flacons</div>
                            <div class="text-xs text-neutral-400 mt-1">Return Due: {{ $showcase->return_date }}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-neutral-900 text-sm">${{ number_format($showcase->trunk_valuation, 2) }}</div>
                            <span class="badge bg-primary-50 text-primary-600 text-xs mt-1">
                                {{ $showcase->status }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="p-20 text-center text-neutral-400 text-sm">No trunks currently out of the vault.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection