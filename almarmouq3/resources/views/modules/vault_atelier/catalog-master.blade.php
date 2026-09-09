@extends('layout.layout')

@php
    $title = 'Sovereign 4-Tier Catalog & Quotation Master';
    $subTitle = 'Master Stock Catalog';
@endphp

@section('content')
    <div class="d-flex flex-column gap-4">

        {{-- Top Header Banner --}}
        <div class="card p-24 radius-12 border-0" style="background: #FDFBF7; border: 1px solid #F3EDE2 !important;">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <span class="badge bg-warning-100 text-warning-800 text-xs fw-bold px-2 py-1 mb-1">BOX GCC-BLR-01 (6.89
                        KG)</span>
                    <h4 class="fw-bold mb-0">OPERATING STOCK LAUNCH (6.8885 KG)</h4>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-neutral-200 text-neutral-800 px-3 py-2 radius-8">Working Cost: 12.5% Active</span>
                </div>
            </div>

            {{-- Master Telemetry Row --}}
            <div class="row g-3 text-center">
                <div class="col-6 col-md-4 col-xl-2">
                    <span class="text-xs text-neutral-500 text-uppercase">Total Stock Weight</span>
                    <h5 class="fw-bold text-neutral-900 mt-1 mb-0">{{ number_format($totalWeightKg, 4) }} KG</h5>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <span class="text-xs text-neutral-500 text-uppercase">Acquisition CAPEX</span>
                    <h5 class="fw-bold text-neutral-900 mt-1 mb-0">AED {{ number_format($acquisitionCapex, 2) }}</h5>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <span class="text-xs text-neutral-500 text-uppercase">Working Cost (12.5%)</span>
                    <h5 class="fw-bold text-neutral-900 mt-1 mb-0">AED {{ number_format($workingCostAed, 2) }}</h5>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <span class="text-xs text-neutral-500 text-uppercase">Total Landed Cost</span>
                    <h5 class="fw-bold text-neutral-900 mt-1 mb-0">AED {{ number_format($totalLandedCost, 2) }}</h5>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <span class="text-xs text-neutral-500 text-uppercase">Projected Turnover</span>
                    <h5 class="fw-bold text-primary-600 mt-1 mb-0">AED {{ number_format($projectedGrossTurnover, 2) }}</h5>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <span class="text-xs text-neutral-500 text-uppercase">Projected Profit</span>
                    <h5 class="fw-bold text-success-600 mt-1 mb-0">AED {{ number_format($projectedGrossProfit, 2) }}</h5>
                </div>
            </div>
        </div>

        {{-- Tier 1: 4 Product Class Cards --}}
        <div class="row g-3">
            @foreach ($catalogTiers as $tier)
                <div class="col-12 col-md-6 col-xl-3">
                    <div
                        class="card h-100 p-20 radius-12 border border-neutral-200 shadow-xs d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="fw-bold mb-0">{{ $tier['name'] }}</h5>
                                <iconify-icon icon="solar:crown-line-duotone"
                                    class="text-warning-600 text-xl"></iconify-icon>
                            </div>
                            <div class="p-12 radius-8 bg-neutral-50 border border-neutral-100 mb-3">
                                <div class="d-flex justify-content-between text-xs mb-1">
                                    <span class="text-neutral-500">Available Stock:</span>
                                    <span class="fw-bold">{{ number_format($tier['weight_kg'], 3) }} KG</span>
                                </div>
                                <div class="d-flex justify-content-between text-xs mb-1">
                                    <span class="text-neutral-500">Landed Cost/KG:</span>
                                    <span class="fw-bold">AED {{ number_format($tier['avg_landed_cost'], 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between text-xs">
                                    <span class="text-neutral-500">Selling Price/KG:</span>
                                    <span class="fw-bold text-success-600">AED
                                        {{ number_format($tier['selling_price_kg'], 2) }}</span>
                                </div>
                            </div>

                            <h6 class="text-xs text-uppercase fw-bold text-neutral-400 mb-2">Package Variations</h6>
                            <div class="d-flex flex-column gap-2 mb-3">
                                @foreach ($tier['packages'] as $pkgKey => $price)
                                    <div
                                        class="d-flex justify-content-between align-items-center text-xs p-8 radius-6 border border-neutral-100">
                                        <span>{{ ucwords(str_replace('_', ' ', $pkgKey)) }}</span>
                                        <span class="fw-bold">AED {{ number_format($price) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Complimentary Teaser Box --}}
                        <div class="p-10 radius-8 border border-warning-200" style="background-color: #FFFDF9;">
                            <div class="text-2xs fw-bold text-warning-800 text-uppercase">Complimentary Sampler</div>
                            <div class="text-xs text-neutral-700 mt-1">{{ $tier['teaser'] }}</div>
                            <div class="text-2xs text-neutral-400 mt-1">&bull; Stock auto-deducted on sale</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Tier 3: The 15-Item Breakdown Register --}}
        <div class="card p-24 radius-12 border-0 shadow-xs">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h6 class="fw-bold mb-0">Box GCC-BLR-01 Variety & Sub-Class Breakdown</h6>
                    <span class="text-xs text-neutral-400">15 Line Items from Supplier Manifest</span>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle text-sm">
                    <thead>
                        <tr class="text-neutral-500 text-xs text-uppercase">
                            <th>SL</th>
                            <th>Variety / Cut</th>
                            <th>Class Tier</th>
                            <th>Supplier Grade</th>
                            <th>Weight (KG)</th>
                            <th>Base Rate</th>
                            <th>Landed Cost (+12.5%)</th>
                            <th>Margin</th>
                            <th>Selling / KG</th>
                            <th>1 Toula (11.66g)</th>
                            <th>Projected Retail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rawItems as $item)
                            @php
                                $sellingKg = $item->landed_cost_aed / (1 - $item->margin_pct / 100);
                                $toulaRate = ($sellingKg / 1000) * 11.6638;
                                $projectedItemRetail = $item->weight_kg * $sellingKg;
                            @endphp
                            <tr>
                                <td class="text-neutral-400">{{ $item->item_index }}</td>
                                <td class="fw-bold">{{ $item->variety_cut }}</td>
                                <td><span class="badge bg-primary-50 text-primary-700">{{ $item->tier_class }}</span></td>
                                <td>{{ $item->supplier_grade }}</td>
                                <td class="fw-bold">{{ number_format($item->weight_kg, 4) }}</td>
                                <td>AED {{ number_format($item->base_rate_aed, 2) }}</td>
                                <td>AED {{ number_format($item->landed_cost_aed, 2) }}</td>
                                <td>{{ $item->margin_pct }}%</td>
                                <td class="fw-bold">AED {{ number_format($sellingKg, 2) }}</td>
                                <td class="fw-bold text-warning-700">AED {{ number_format($toulaRate) }}</td>
                                <td class="fw-bold text-success-600">AED {{ number_format($projectedItemRetail, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection
