@extends('layout.layout')
@php
    $title='Star Ratings';
    $subTitle = 'Components / Star Ratings';
@endphp

@section('content')

            <div class="row gy-4">
                <div class="col-sm-6">
                    <div class="card h-100 p-0">
                        <div class="card-header border-bottom bg-base py-16 px-24">
                            <h6 class="text-lg fw-semibold mb-0">Default Star Ratings</h6>
                        </div>
                        <div class="card-body p-24">
                            <ul class="d-flex flex-wrap align-items-center gap-12">
                                <li class="text-warning-600 text-2xxl line-height-1">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                                <li class="text-warning-600 text-2xxl line-height-1">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                                <li class="text-warning-600 text-2xxl line-height-1">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                                <li class="text-warning-600 text-2xxl line-height-1">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                                <li class="text-warning-600 text-2xxl line-height-1">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card h-100 p-0">
                        <div class="card-header border-bottom bg-base py-16 px-24">
                            <h6 class="text-lg fw-semibold mb-0">Half Star</h6>
                        </div>
                        <div class="card-body p-24">
                            <ul class="d-flex flex-wrap align-items-center gap-12">
                                <li class="text-warning-600 text-2xxl line-height-1">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                                <li class="text-warning-600 text-2xxl line-height-1">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                                <li class="text-warning-600 text-2xxl line-height-1">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                                <li class="text-warning-600 text-2xxl line-height-1">
                                    <iconify-icon icon="mdi:star-outline"></iconify-icon>
                                </li>
                                <li class="text-warning-600 text-2xxl line-height-1">
                                    <iconify-icon icon="mdi:star-outline"></iconify-icon>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card h-100 p-0">
                        <div class="card-header border-bottom bg-base py-16 px-24">
                            <h6 class="text-lg fw-semibold mb-0">Multi Color</h6>
                        </div>
                        <div class="card-body p-24">
                            <ul class="d-flex flex-wrap align-items-center gap-12">
                                <li class="text-2xxl line-height-1 text-primary-600">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                                <li class="text-2xxl line-height-1 text-lilac-600">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                                <li class="text-2xxl line-height-1 text-success-600">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                                <li class="text-2xxl line-height-1 text-info-600">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                                <li class="text-2xxl line-height-1 text-danger-600">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card h-100 p-0">
                        <div class="card-header border-bottom bg-base py-16 px-24">
                            <h6 class="text-lg fw-semibold mb-0">Rating</h6>
                        </div>
                        <div class="card-body p-24">
                            <ul class="d-flex flex-wrap align-items-center gap-12">
                                <li class="text-warning-600 text-2xxl line-height-1">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                                <li class="text-warning-600 text-2xxl line-height-1">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                                <li class="text-neutral-400 text-2xxl line-height-1">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                                <li class="text-neutral-400 text-2xxl line-height-1">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                                <li class="text-neutral-400 text-2xxl line-height-1">
                                    <iconify-icon icon="material-symbols:star"></iconify-icon>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

@endsection
