@extends('layout.layout')
@php
    $title='Column Chart';
    $subTitle = 'Components / Column Chart';
    $script = ' <script src="' . asset('assets/js/columnChartPageChart.js') . '"></script>';
@endphp

@section('content')

            <div class="row gy-4">
                <div class="col-md-6">
                    <div class="card h-100 p-0">
                        <div class="card-header border-bottom bg-base py-16 px-24">
                            <h6 class="text-lg fw-semibold mb-0">Column Charts</h6>
                        </div>
                        <div class="card-body p-24">
                            <div id="columnChart" class=""></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 p-0">
                        <div class="card-header border-bottom bg-base py-16 px-24">
                            <h6 class="text-lg fw-semibold mb-0">Column Charts</h6>
                        </div>
                        <div class="card-body p-24">
                            <div id="columnGroupBarChart" class=""></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 p-0">
                        <div class="card-header border-bottom bg-base py-16 px-24">
                            <h6 class="text-lg fw-semibold mb-0">Group Column</h6>
                        </div>
                        <div class="card-body p-24">
                            <div id="groupColumnBarChart" class=""></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100 p-0">
                        <div class="card-header border-bottom bg-base py-16 px-24">
                            <h6 class="text-lg fw-semibold mb-0">Simple Column</h6>
                        </div>
                        <div class="card-body p-24">
                            <div id="upDownBarchart"></div>
                        </div>
                    </div>
                </div>
            </div>

@endsection