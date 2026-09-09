@extends('layout.layout')

@php
    $title = __('entities.modules.portal');
    $subTitle = __('entities.sidebar.orders_quotes');
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24">
                <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.orders_quotes') }}</h4>
                <p class="text-sm text-secondary-light mb-0">Client Active Orders, Pending Quotation Approvals & Historical Statements</p>
            </div>
        </div>
    </div>
</div>
@endsection
