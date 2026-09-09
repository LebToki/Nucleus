@extends('layout.layout')

@php
    $title = __('entities.modules.financial');
    $subTitle = __('entities.sidebar.cogs_expenses');
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.cogs_expenses') }}</h4>
                    <p class="text-sm text-secondary-light mb-0">Raw Sourcing Accounts, Atelier Expenses, Crystal Packaging & Logistics Disbursements</p>
                </div>
                <button type="button" class="btn btn-primary-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:add-circle-outline" class="text-xl"></iconify-icon>
                    <span>Record Expense Voucher</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12">
            <div class="card-body p-24">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Voucher #</th>
                                <th>Supplier / Vendor</th>
                                <th>Category</th>
                                <th>Amount (AED)</th>
                                <th>Date</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-semibold text-primary-600">#EXP-2026-081</td>
                                <td>Kalimantan Oud Distillates Ltd</td>
                                <td><span class="badge bg-neutral-100 text-neutral-800">Raw Agarwood</span></td>
                                <td class="fw-bold">AED 185,000</td>
                                <td>2026-09-02</td>
                                <td>Bank Wire Transfer</td>
                                <td><span class="badge bg-success-50 text-success-600 px-10 py-4 radius-4 text-xs">Approved</span></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-primary-600">#EXP-2026-082</td>
                                <td>Baccarat Crystal Atelier</td>
                                <td><span class="badge bg-neutral-100 text-neutral-800">Custom Decanters</span></td>
                                <td class="fw-bold">AED 92,500</td>
                                <td>2026-09-05</td>
                                <td>Letter of Credit</td>
                                <td><span class="badge bg-success-50 text-success-600 px-10 py-4 radius-4 text-xs">Approved</span></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-primary-600">#EXP-2026-083</td>
                                <td>Emirates Armored Courier Service</td>
                                <td><span class="badge bg-neutral-100 text-neutral-800">White-Glove Transport</span></td>
                                <td class="fw-bold">AED 24,000</td>
                                <td>2026-09-07</td>
                                <td>Corporate Account</td>
                                <td><span class="badge bg-warning-50 text-warning-600 px-10 py-4 radius-4 text-xs">Pending Audit</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
