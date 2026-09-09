@extends('layout.layout')

@php
    $title = __('entities.modules.vault_atelier');
    $subTitle = __('entities.sidebar.vip_waiting_list');
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24">
                <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.vip_waiting_list') }}</h4>
                <p class="text-sm text-secondary-light mb-0">Waitlist Allocations for Curing Distillates & Limited Harvest Batches</p>
            </div>
        </div>
    </div>
</div>
@endsection
