@extends('layout.layout')

@php
    $title = __('entities.modules.vault_atelier');
    $subTitle = __('entities.sidebar.price_history');
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24">
                <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.price_history') }}</h4>
                <p class="text-sm text-secondary-light mb-0">Historical Valuation of Aged Oils, Tola Price Trends & Commodity Benchmarks</p>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 p-24">
            <h6 class="text-md fw-semibold text-primary-light mb-16">Vintage Oud Valuation Movement (2020 - 2026)</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Agarwood Vintage Batch</th>
                            <th>2022 Price</th>
                            <th>2024 Price</th>
                            <th>2026 Price</th>
                            <th>Appreciation Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold">Wild Royal Cambodi 1984</td>
                            <td>AED 28,000 / Tola</td>
                            <td>AED 36,000 / Tola</td>
                            <td class="fw-bold text-success-600">AED 45,000 / Tola</td>
                            <td><span class="badge bg-success-50 text-success-600">+60.7%</span></td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Koh Kong Wild Distillate 1995</td>
                            <td>AED 22,000 / Tola</td>
                            <td>AED 29,000 / Tola</td>
                            <td class="fw-bold text-success-600">AED 38,000 / Tola</td>
                            <td><span class="badge bg-success-50 text-success-600">+72.7%</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
