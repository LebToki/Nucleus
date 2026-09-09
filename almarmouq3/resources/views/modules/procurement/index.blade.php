@extends('layouts.app')

@section('content')
<div class="d-flex flex-column gap-4">

    {{-- Section Header --}}
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h4 class="fw-bold mb-1">Procurement & Sourcing Cockpit</h4>
            <p class="text-neutral-500 text-sm mb-0">Supplier purchase orders, inbound border shipments, GRN receipts & stock runout telemetry</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-neutral-200 text-neutral-800 px-3 py-2 radius-8">
                <iconify-icon icon="solar:box-minimalistic-outline" class="me-1 align-middle text-primary-600"></iconify-icon>
                Active Inbound Pipeline
            </span>
        </div>
    </div>

    {{-- Tier 1: 4 KPI Cards --}}
    <div class="row g-3">
        {{-- Card 1: Active Qualified Suppliers --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #FFF7ED;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-warning-700 text-uppercase">Distillery & Wood Sources</span>
                        <h3 class="fw-bold text-neutral-900 mt-2 mb-1">9</h3>
                        <span class="text-xs text-neutral-500">Qualified distillers & artisans</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-warning-700 bg-white shadow-xs">
                        <iconify-icon icon="solar:shop-2-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: In-Transit Consignments --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #ECFDF5;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-teal-700 text-uppercase">Inbound Consignments</span>
                        <h3 class="fw-bold text-neutral-900 mt-2 mb-1">4</h3>
                        <span class="text-xs text-neutral-500">Shipments en route to vault</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-teal-700 bg-white shadow-xs">
                        <iconify-icon icon="solar:inbox-in-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Cleared Goods Received ("What Arrived / Got") --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #F0FDF4;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-success-700 text-uppercase">Accepted Receipts (GRN)</span>
                        <h3 class="fw-bold text-success-600 mt-2 mb-1">$218,400.00</h3>
                        <span class="text-xs text-neutral-500">Stock safely vaulted this month</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-success-600 bg-white shadow-xs">
                        <iconify-icon icon="solar:check-circle-outline" class="text-xl"></iconify-icon>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Committed LPOs ("What We Owe / Committed") --}}
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100 p-20 radius-12 border-0" style="background-color: #FEF2F2;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <span class="text-xs fw-semibold text-danger-700 text-uppercase">Committed LPO Capital</span>
                        <h3 class="fw-bold text-danger-600 mt-2 mb-1">$74,850.00</h3>
                        <span class="text-xs text-neutral-500">Issued orders awaiting delivery</span>
                    </div>
                    <div class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-danger-600 bg-white shadow-xs">
                        <iconify-icon icon="solar:bill-cross-outline" class="text-xl"></iconify-icon>
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
                    <h6 class="fw-semibold mb-0">Procurement Spend vs Budget Cap</h6>
                    <span class="text-xs text-neutral-400">Monthly Committed LPOs</span>
                </div>
                <div id="chart-procurement-spend" style="min-height: 220px;"></div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card p-24 radius-12 border-0 shadow-xs h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-semibold mb-0">Stock Runout vs Replenishment Lead Time</h6>
                    <span class="text-xs text-neutral-400">Bottling Components & Crystal Flacons</span>
                </div>
                <div id="chart-lead-time" style="min-height: 220px;"></div>
            </div>
        </div>
    </div>

    {{-- Tier 3: 2 Telemetry Feeds --}}
    <div class="row g-3">
        {{-- Feed 1: Pending Inbound Shipments (GRN Expected) --}}
        <div class="col-12 col-lg-6">
            <div class="card p-20 radius-12 border-0 shadow-xs">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">Inbound Shipments in Transit</h6>
                    <span class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">Customs & Transit</span>
                </div>
                <div class="d-flex flex-column gap-2 overflow-y-auto pe-1" style="max-height: 380px;">
                    @forelse ($inboundShipments ?? [] as $shipment)
                    <div class="p-12 radius-8 border border-neutral-200 d-flex justify-content-between align-items-center hover-bg-neutral-50 transition-all">
                        <div>
                            <div class="fw-bold text-neutral-900 text-sm">{{ $shipment->tracking_reference }}</div>
                            <div class="text-xs text-neutral-500 mt-1">{{ $shipment->supplier_name }} &bull; {{ $shipment->cargo_manifest }}</div>
                            <div class="text-xs text-neutral-400 mt-1">ETA: {{ $shipment->estimated_arrival }}</div>
                        </div>
                        <span class="badge bg-primary-50 text-primary-600 text-xs px-2 py-1">
                            {{ $shipment->customs_status }}
                        </span>
                    </div>
                    @empty
                    <div class="p-20 text-center text-neutral-400 text-sm">No incoming consignments recorded.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Feed 2: Recent Purchase Orders (LPOs) --}}
        <div class="col-12 col-lg-6">
            <div class="card p-20 radius-12 border-0 shadow-xs">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">Recent Purchase Orders</h6>
                    <span class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">Issued LPOs</span>
                </div>
                <div class="d-flex flex-column gap-2 overflow-y-auto pe-1" style="max-height: 380px;">
                    @forelse ($recentOrders ?? [] as $order)
                    <div class="p-12 radius-8 border border-neutral-200 d-flex justify-content-between align-items-center hover-bg-neutral-50 transition-all">
                        <div>
                            <div class="fw-bold text-neutral-900 text-sm">{{ $order->lpo_number }}</div>
                            <div class="text-xs text-neutral-500 mt-1">{{ $order->vendor_name }} &bull; {{ $order->items_summary }}</div>
                            <div class="text-xs text-neutral-400 mt-1">Issued: {{ $order->created_at->format('Y-m-d') }}</div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-neutral-900 text-sm">${{ number_format($order->total_amount, 2) }}</div>
                            <span class="badge {{ $order->status === 'Approved' ? 'bg-success-50 text-success-600' : 'bg-warning-50 text-warning-700' }} text-xs mt-1">
                                {{ $order->status }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="p-20 text-center text-neutral-400 text-sm">No purchase orders created recently.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection