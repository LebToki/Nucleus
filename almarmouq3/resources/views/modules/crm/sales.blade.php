@extends('layout.layout')

@php
    $title = __('entities.modules.crm');
    $subTitle = __('entities.sidebar.sales_ledger');
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.sales_ledger') }}</h4>
                    <p class="text-sm text-secondary-light mb-0">Live Sales Transactions, Order Fulfillment Audits & Revenue Receipts</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12">
            <div class="card-body p-24">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order Ref</th>
                                <th>Client Name</th>
                                <th>Item Configured</th>
                                <th>Amount (AED)</th>
                                <th>Date</th>
                                <th>Fulfillment</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-semibold text-primary-600">#ORD-8801</td>
                                <td>H.H. Sheikh Al-Maktoum House</td>
                                <td>10 Tolas Royal Cambodi 1984 + Baccarat Casket</td>
                                <td class="fw-bold text-success-600">AED 495,000</td>
                                <td>2026-09-08</td>
                                <td><span class="badge bg-success-50 text-success-600">Delivered via White-Glove</span></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-primary-600">#ORD-8802</td>
                                <td>Emirates Diplomatic Academy</td>
                                <td>50 Custom Engraved Miniatures</td>
                                <td class="fw-bold text-success-600">AED 180,000</td>
                                <td>2026-09-09</td>
                                <td><span class="badge bg-primary-50 text-primary-600">In Transit</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
