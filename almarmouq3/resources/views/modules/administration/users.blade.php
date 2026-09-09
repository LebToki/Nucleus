@extends('layout.layout')

@php
    $title = __('entities.modules.administration');
    $subTitle = __('entities.sidebar.team_users');
@endphp

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm radius-12 bg-neutral-0">
            <div class="card-body p-24">
                <h4 class="text-lg fw-semibold text-primary-light mb-1">{{ __('entities.sidebar.team_users') }}</h4>
                <p class="text-sm text-secondary-light mb-0">Boutique Staff, Curator Accounts, Executive Assistants & Security Personnel</p>
            </div>
        </div>
    </div>
</div>
@endsection
