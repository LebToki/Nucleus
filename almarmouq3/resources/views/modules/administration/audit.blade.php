@extends('layout.layout')

@php
    $title = __('entities.modules.administration');
    $subTitle = __('entities.sidebar.audit_trail');
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24">
                <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.audit_trail') }}</h4>
                <p class="text-sm text-secondary-light mb-0">System Security Audit Logs, Access Telemetry & Administrative Changes</p>
            </div>
        </div>
    </div>
</div>
@endsection
