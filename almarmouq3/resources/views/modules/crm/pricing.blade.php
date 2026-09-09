@extends('layout.layout')

@php
    $title = __('entities.modules.crm');
    $subTitle = __('entities.sidebar.price_tiers');
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24 justify-content-between align-items-center flex-wrap gap-3">
                <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.price_tiers') }}</h4>
                <p class="text-sm text-secondary-light mb-0">Commercial Tola Pricing Grid, Rarity Multipliers & Volume Discount Rules</p>
            </div>
        </div>
    </div>

    <!-- Pricing Matrix Cards -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm radius-12 p-24 text-center">
            <h5 class="text-md fw-bold text-neutral-800 mb-2">Wild Vintage Oud Oils</h5>
            <span class="text-2xl fw-bold text-primary-600 mb-16">AED 35,000 / Tola</span>
            <ul class="list-unstyled text-sm text-secondary-light text-start mb-24">
                <li class="py-6 border-bottom"><iconify-icon icon="solar:check-circle-bold" class="text-success-600 me-2"></iconify-icon> Minimum 30+ Year Aged Distillates</li>
                <li class="py-6 border-bottom"><iconify-icon icon="solar:check-circle-bold" class="text-success-600 me-2"></iconify-icon> Wild Harvested Agarwood Heartwood</li>
                <li class="py-6"><iconify-icon icon="solar:check-circle-bold" class="text-success-600 me-2"></iconify-icon> Hand-numbered COA Certificate</li>
            </ul>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm radius-12 p-24 text-center border border-primary-500">
            <div class="badge bg-primary-600 text-white px-12 py-4 radius-4 text-xs fw-semibold mb-12 d-inline-block">Most Requested</div>
            <h5 class="text-md fw-bold text-neutral-800 mb-2">Taifi & Floral Attars</h5>
            <span class="text-2xl fw-bold text-primary-600 mb-16">AED 12,000 / Tola</span>
            <ul class="list-unstyled text-sm text-secondary-light text-start mb-24">
                <li class="py-6 border-bottom"><iconify-icon icon="solar:check-circle-bold" class="text-success-600 me-2"></iconify-icon> First Harvest Saudi Taifi Rose</li>
                <li class="py-6 border-bottom"><iconify-icon icon="solar:check-circle-bold" class="text-success-600 me-2"></iconify-icon> Pure Ambergris & Musks</li>
                <li class="py-6"><iconify-icon icon="solar:check-circle-bold" class="text-success-600 me-2"></iconify-icon> Sovereign Crystal Vials</li>
            </ul>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm radius-12 p-24 text-center">
            <h5 class="text-md fw-bold text-neutral-800 mb-2">Curated Agarwood Chips</h5>
            <span class="text-2xl fw-bold text-primary-600 mb-16">AED 8,500 / Ounces</span>
            <ul class="list-unstyled text-sm text-secondary-light text-start mb-24">
                <li class="py-6 border-bottom"><iconify-icon icon="solar:check-circle-bold" class="text-success-600 me-2"></iconify-icon> Sinking Grade Double Super Wood</li>
                <li class="py-6 border-bottom"><iconify-icon icon="solar:check-circle-bold" class="text-success-600 me-2"></iconify-icon> Zero Resins or Additives</li>
                <li class="py-6"><iconify-icon icon="solar:check-circle-bold" class="text-success-600 me-2"></iconify-icon> Sealed Mahogany Vault Boxes</li>
            </ul>
        </div>
    </div>
</div>
@endsection
