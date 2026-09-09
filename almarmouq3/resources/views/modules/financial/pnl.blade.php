@extends('layout.layout')

@php
    $title = __('entities.modules.financial');
    $subTitle = __('entities.sidebar.pnl');
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.pnl') }}</h4>
                    <p class="text-sm text-secondary-light mb-0">Profit & Loss Statement, Revenue Streams, COGS Breakdown & Operational Margins</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary radius-8 px-16 py-9">Export PDF</button>
                    <button type="button" class="btn btn-primary-600 radius-8 px-16 py-9">Generate Fiscal Audit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- P&L Metric Cards -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm radius-12 p-20 bg-neutral-0">
            <span class="text-secondary-light text-xs fw-medium text-uppercase">Gross Revenue (YTD)</span>
            <h3 class="text-xl fw-bold text-success-600 mt-2 mb-1">AED 4,250,000</h3>
            <span class="text-xs text-success-600 fw-semibold"><iconify-icon icon="solar:arrow-up-linear"></iconify-icon> +18.4% vs last period</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm radius-12 p-20 bg-neutral-0">
            <span class="text-secondary-light text-xs fw-medium text-uppercase">Total COGS (Oud & Distillation)</span>
            <h3 class="text-xl fw-bold text-danger-600 mt-2 mb-1">AED 1,420,000</h3>
            <span class="text-xs text-secondary-light fw-medium">33.4% of Revenue</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm radius-12 p-20 bg-neutral-0">
            <span class="text-secondary-light text-xs fw-medium text-uppercase">Operating Expenses</span>
            <h3 class="text-xl fw-bold text-warning-600 mt-2 mb-1">AED 680,000</h3>
            <span class="text-xs text-secondary-light fw-medium">16.0% of Revenue</span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm radius-12 p-20 bg-neutral-0">
            <span class="text-secondary-light text-xs fw-medium text-uppercase">Net Sovereign Profit</span>
            <h3 class="text-xl fw-bold text-primary-600 mt-2 mb-1">AED 2,150,000</h3>
            <span class="text-xs text-primary-600 fw-semibold">50.6% Net Margin</span>
        </div>
    </div>

    <!-- P&L Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12">
            <div class="card-header bg-neutral-0 border-bottom border-neutral-200 py-16 px-24">
                <h6 class="text-md fw-semibold text-primary-light mb-0">Financial Statement Breakdown (Q3 2026)</h6>
            </div>
            <div class="card-body p-24">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Q1 (AED)</th>
                                <th>Q2 (AED)</th>
                                <th>Q3 (AED)</th>
                                <th class="text-end">Total YTD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-semibold text-success-600">Revenue</td>
                                <td>Bespoke Royal Oud & Crystal Editions</td>
                                <td>1,200,000</td>
                                <td>1,450,000</td>
                                <td>1,600,000</td>
                                <td class="text-end fw-bold">4,250,000</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-danger-600">COGS</td>
                                <td>Agarwood Sourcing, Curing & Aging</td>
                                <td>(400,000)</td>
                                <td>(480,000)</td>
                                <td>(540,000)</td>
                                <td class="text-end fw-bold text-danger-600">(1,420,000)</td>
                            </tr>
                            <tr class="table-light">
                                <td class="fw-bold text-primary-600">Gross Margin</td>
                                <td class="fw-medium">Revenue Less Direct Sourcing Cost</td>
                                <td>800,000</td>
                                <td>970,000</td>
                                <td>1,060,000</td>
                                <td class="text-end fw-bold text-primary-600">2,830,000</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-warning-600">OpEx</td>
                                <td>Logistics, Boutique Payroll & Majlis Receptions</td>
                                <td>(210,000)</td>
                                <td>(230,000)</td>
                                <td>(240,000)</td>
                                <td class="text-end fw-bold text-warning-600">(680,000)</td>
                            </tr>
                            <tr class="bg-primary-50">
                                <td class="fw-bold text-primary-700">Net Operating Income</td>
                                <td class="fw-bold text-primary-700">Sovereign Net Income Before Tax</td>
                                <td class="fw-bold text-primary-700">590,000</td>
                                <td class="fw-bold text-primary-700">740,000</td>
                                <td class="fw-bold text-primary-700">820,000</td>
                                <td class="text-end fw-bold text-primary-700">2,150,000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
