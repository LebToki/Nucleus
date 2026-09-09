@extends('layout.layout')

@php
    $title = __('entities.modules.crm');
    $subTitle = __('entities.sidebar.quote_builder');
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.quote_builder') }}</h4>
                    <p class="text-sm text-secondary-light mb-0">Bespoke Oud Formula Configuration, Crystal Packaging Selection & Formal Quotation Generation</p>
                </div>
                <button type="button" class="btn btn-success-600 radius-8 px-20 py-11 d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:disk-line-duotone" class="text-xl"></iconify-icon>
                    <span>Save & Issue Quotation</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Interactive Form -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm radius-12 p-24">
            <h6 class="text-md fw-semibold text-primary-light mb-20">Quotation Configuration</h6>
            <form action="#" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-sm fw-medium text-neutral-800">Target VIP Client / Entity</label>
                        <select class="form-select radius-8">
                            <option>H.H. Sheikh Al-Maktoum House</option>
                            <option>H.E. Minister of Protocol</option>
                            <option>Emirates Diplomatic Academy</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-sm fw-medium text-neutral-800">Quotation Validity</label>
                        <input type="date" class="form-control radius-8" value="2026-10-09">
                    </div>
                    <div class="col-12">
                        <label class="form-label text-sm fw-medium text-neutral-800">Selected Dehn Oud & Oil Batches</label>
                        <select class="form-select radius-8" multiple style="height: 100px;">
                            <option selected>Royal Cambodi Vintage 1984 (10 Tolas) - AED 45,000 / Tola</option>
                            <option selected>Taifi Rose First Distillation 2025 (5 Tolas) - AED 12,000 / Tola</option>
                            <option>Wild Koh Kong Super Grade (20 Tolas) - AED 38,000 / Tola</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-sm fw-medium text-neutral-800">Packaging & Crystal Casket</label>
                        <select class="form-select radius-8">
                            <option>Hand-Cut Baccarat Crystal Decanter with 24k Gold Engraving</option>
                            <option>Sovereign Velvet & Agarwood Casket</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-sm fw-medium text-neutral-800">Commercial Margin Tier</label>
                        <select class="form-select radius-8">
                            <option>Royal Sovereign Tier (0% Discount)</option>
                            <option>Diplomatic Preferred Tier (5% Protocol Adjustment)</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Realtime Summary -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm radius-12 p-24 bg-primary-50">
            <h6 class="text-md fw-semibold text-primary-800 mb-16">Real-Time Quotation Breakdown</h6>
            <div class="d-flex justify-content-between py-8">
                <span class="text-sm text-neutral-700">Rare Oud Oil Subtotal</span>
                <span class="text-sm fw-bold">AED 510,000</span>
            </div>
            <div class="d-flex justify-content-between py-8">
                <span class="text-sm text-neutral-700">Custom Crystal Decanters</span>
                <span class="text-sm fw-bold">AED 45,000</span>
            </div>
            <div class="d-flex justify-content-between py-8 border-bottom border-neutral-300">
                <span class="text-sm text-neutral-700">VAT (5%)</span>
                <span class="text-sm fw-bold">AED 27,750</span>
            </div>
            <div class="d-flex justify-content-between pt-16">
                <span class="text-base fw-bold text-primary-900">Total Quotation Value</span>
                <span class="text-base fw-bold text-primary-900">AED 582,750</span>
            </div>
        </div>
    </div>
</div>
@endsection
