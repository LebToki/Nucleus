@extends('layout.layout')

@php
    $title = __('entities.dashboard.title');
    $subTitle = __('entities.sidebar.dashboard_overview');

    $revenueLabels = json_encode($charts['revenue_labels'] ?? []);
    $revenueData = json_encode($charts['revenue_data'] ?? []);
    $channelLabels = json_encode($charts['channel_labels'] ?? []);
    $channelData = json_encode($charts['channel_data'] ?? []);

    ob_start();
@endphp
<script>
    function getThemeColors() {
        var isDark = document.documentElement.getAttribute("data-theme") === "dark";
        if (isDark) {
            return {
                grid: "#3f4b5b"
            };
        }
        return {
            grid: "#e0e0e0"
        };
    }

    var revenueOptions = {
        series: [{
            name: "Revenue (AED)",
            data: {!! $revenueData !!}
        }],
        chart: {
            type: "line",
            height: 280,
            toolbar: {
                show: false
            }
        },
        stroke: {
            curve: "smooth",
            width: 3
        },
        markers: {
            size: 4
        },
        xaxis: {
            categories: {!! $revenueLabels !!},
            labels: {
                style: {
                    cssClass: "text-xs"
                }
            }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return val.toLocaleString("en-US");
                }
            }
        },
        grid: {
            borderColor: getThemeColors().grid,
            strokeDashArray: 3
        },
        dataLabels: {
            enabled: false
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val.toLocaleString("en-US") + " AED";
                }
            }
        }
    };

    var revenueChart = new ApexCharts(document.querySelector("#chart-revenue-velocity"), revenueOptions);
    revenueChart.render();

    var channelColors = ["var(--success-600)", "var(--warning-600)", "var(--info-600)", "var(--danger-600)"];
    var rawChannelData = {!! $channelData !!};
    var rawChannelLabels = !!$channelLabels!!
    };
    var channelOptions = {
        series: rawChannelData,
        chart: {
            type: "donut",
            height: 280
        },
        labels: rawChannelLabels,
        legend: {
            position: "bottom",
            markers: {
                width: 10,
                height: 10
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val.toFixed(1) + "%";
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val;
                }
            }
        },
        colors: channelColors.slice(0, rawChannelData.length || 1)
    };

    var channelChart = new ApexCharts(document.querySelector("#chart-channel-mix"), channelOptions);
    channelChart.render();

    document.addEventListener("theme-change", function() {
        var colors = getThemeColors();
        revenueChart.updateOptions({
            grid: {
                borderColor: colors.grid
            }
        });
        channelChart.updateColors();
    });
</script>
@php
    $script = ob_get_clean();
@endphp

@section('content')
    <!-- Main Container -->
    <div class="d-flex flex-column gap-4">
        <!-- Header Section -->
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <div class="fw-bold text-neutral-900 text-lg mb-1">{{ __('entities.dashboard.title') }}</div>
                <div class="text-neutral-500 text-sm">{{ __('entities.dashboard.business_glance') }}</div>
            </div>
            <span class="bg-neutral-100 text-neutral-600 text-xs px-2 py-1">
                <iconify-icon icon="solar:shield-check-outline" class="me-1 align-middle"></iconify-icon>
                {{ __('entities.dashboard.secure_session') }}
            </span>
        </div>
        <!-- End Header Section -->

        {{-- Tier 1: 4 KPI Cards --}}
        <div class="row g-3">
            <!-- Revenue Card -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 p-20 radius-12 border-0" style="background-color: var(--info-50);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span
                                class="text-xs fw-semibold text-info-700 text-uppercase">{{ __('entities.dashboard.revenue') }}
                                (AED)</span>
                            <div class="fw-bold text-neutral-900 mt-2 mb-1 fs-2">
                                {{ number_format($metrics['revenue'], 0, '.', ',') }}</div>
                            <div
                                class="text-xs text-neutral-500 mt-1 {{ $metrics['trend'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                @if ($metrics['trend'] >= 0)
                                    <iconify-icon icon="solar:arrow-up-outline" class="text-success-600"></iconify-icon>
                                @else
                                    <iconify-icon icon="solar:arrow-down-outline" class="text-danger-600"></iconify-icon>
                                @endif
                                {{ abs($metrics['trend']) }}% {{ __('entities.dashboard.vs_last_month') }}
                            </div>
                        </div>
                        <div
                            class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-info-600 bg-white shadow-xs">
                            <iconify-icon icon="flat-color-icons:sales-performance" class="text-xl"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Revenue Card -->

            <!-- Pipeline Card -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 p-20 radius-12 border-0" style="background-color: var(--success-50);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span
                                class="text-xs fw-semibold text-success-700 text-uppercase">{{ __('entities.dashboard.pipeline') }}
                                (AED)</span>
                            <div class="fw-bold text-neutral-900 mt-2 mb-1 fs-2">
                                {{ number_format($metrics['pipeline'], 0, '.', ',') }}</div>
                            <div class="text-xs text-neutral-500 mt-1">
                                {{ __('entities.dashboard.opportunities_in_progress') }}</div>
                        </div>
                        <div
                            class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-success-600 bg-white shadow-xs">
                            <iconify-icon icon="material-icon-theme:pipeline" class="text-xl"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Pipeline Card -->

            <!-- Low Stock Card -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 p-20 radius-12 border-0" style="background-color: var(--warning-50);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span
                                class="text-xs fw-semibold text-warning-700 text-uppercase">{{ __('entities.dashboard.low_stock') }}</span>
                            <div class="fw-bold text-neutral-900 mt-2 mb-1 fs-2">{{ $metrics['lowStockCount'] }}</div>
                            <div class="text-xs text-neutral-500 mt-1">
                                {{ number_format($metrics['lowStockValue'] ?? 0, 2, '.', ',') }}
                                {{ __('entities.dashboard.value') }}</div>
                        </div>
                        <div
                            class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-warning-600 bg-white shadow-xs">
                            <iconify-icon icon="solar:box-outline" class="text-xl"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Low Stock Card -->

            <!-- Overdue Card -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card h-100 p-20 radius-12 border-0" style="background-color: var(--danger-50);">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span
                                class="text-xs fw-semibold text-danger-700 text-uppercase">{{ __('entities.dashboard.overdue') }}</span>
                            <div class="fw-bold text-danger-600 mt-2 mb-1 fs-2">
                                {{ number_format($metrics['pendingPayments'], 0, '.', ',') }}</div>
                            <div class="text-xs text-neutral-500 mt-1">{{ $metrics['pendingDeliveryCount'] }}
                                {{ __('entities.dashboard.deliveries_pending') }}</div>
                        </div>
                        <div
                            class="w-40-px h-40-px rounded-circle d-flex align-items-center justify-content-center text-danger-600 bg-white shadow-xs">
                            <iconify-icon icon="solar:danger-circle-outline" class="text-xl"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Overdue Card -->
        </div>
        <!-- End Tier 1: 4 KPI Cards -->

        {{-- Tier 2: Charts --}}
        <div class="row g-3">
            <!-- Revenue Velocity Chart -->
            <div class="col-12 col-lg-6">
                <div class="card p-24 radius-12 border-0 shadow-xs h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-lg mb-0">{{ __('entities.dashboard.revenue_velocity') }}</h6>
                        <span
                            class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">{{ __('entities.dashboard.this_month') }}</span>
                    </div>
                    <div id="chart-revenue-velocity" style="min-height: 280px;"></div>
                </div>
            </div>
            <!-- End Revenue Velocity Chart -->

            <!-- Channel Mix Chart -->
            <div class="col-12 col-lg-6">
                <div class="card p-24 radius-12 border-0 shadow-xs h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-lg mb-0">{{ __('entities.dashboard.channel_mix') }}</h6>
                        <span
                            class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">{{ __('entities.dashboard.all_time') }}</span>
                    </div>
                    <div id="chart-channel-mix" style="min-height: 280px;"></div>
                </div>
            </div>
            <!-- End Channel Mix Chart -->
        </div>
        <!-- End Tier 2: Charts -->

        {{-- Tier 3: Quick Insights --}}
        <div class="row g-3">
            <!-- Conversion Insights -->
            <div class="col-12 col-lg-6">
                <div class="card p-20 radius-12 border-0 shadow-xs">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-lg mb-0">{{ __('entities.dashboard.conversion_insights') }}</h6>
                        <span
                            class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">{{ __('entities.dashboard.monthly') }}</span>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between py-2 border-bottom border-neutral-200">
                            <span class="text-neutral-600 text-sm">{{ __('entities.dashboard.conversion_rate') }}</span>
                            <span class="fw-bold text-neutral-900">24.3%</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom border-neutral-200">
                            <span class="text-neutral-600 text-sm">{{ __('entities.dashboard.leads_value') }}</span>
                            <span class="fw-bold text-neutral-900">28,450 AED</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span
                                class="text-neutral-600 text-sm">{{ __('entities.dashboard.trend_vs_last_month') }}</span>
                            <span class="fw-bold text-success-600">+{{ abs($metrics['trend']) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Conversion Insights -->

            <!-- Incoming Alerts -->
            <div class="col-12 col-lg-6">
                <div class="card p-20 radius-12 border-0 shadow-xs">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-lg mb-0">{{ __('entities.dashboard.incoming_alerts') }}</h6>
                        <span
                            class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">{{ __('entities.dashboard.last_7_days') }}</span>
                    </div>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between py-2 border-bottom border-neutral-200">
                            <span class="text-neutral-600 text-sm">{{ __('entities.dashboard.new_emails') }}</span>
                            <span class="fw-bold text-neutral-900">{{ $metrics['newEmails'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom border-neutral-200">
                            <span class="text-neutral-600 text-sm">{{ __('entities.dashboard.incoming_rfq') }}</span>
                            <span class="fw-bold text-neutral-900">{{ $metrics['incomingRFQs'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-neutral-600 text-sm">{{ __('entities.dashboard.overdue_invoices') }}</span>
                            <span class="fw-bold text-danger-600">{{ $metrics['overdueInvoices'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Incoming Alerts -->
        </div>
        <!-- End Tier 3: Quick Insights -->

        {{-- Tier 4: Low Stock Detail + Pending Deliveries --}}
        <div class="row g-3">
            <!-- Low Stock Detail -->
            <div class="col-12 col-lg-6">
                <div class="card p-20 radius-12 border-0 shadow-xs">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-lg mb-0">{{ __('entities.dashboard.low_stock') }}</h6>
                        <span
                            class="badge bg-warning-50 text-warning-700 text-xs px-2 py-1">{{ $metrics['lowStockCount'] }}
                            {{ __('entities.dashboard.alerts') }}</span>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        @forelse($metrics['lowStockLots'] as $lot)
                            <div
                                class="p-12 radius-8 border border-neutral-200 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-neutral-900 text-sm">
                                        {{ $lot->product->business_name ?? $lot->business_name }}</div>
                                    <div class="text-xs text-neutral-500 mt-1">{{ number_format($lot->quantity_kg, 2) }}
                                        {{ __('entities.dashboard.kg_remaining') }} • Lot
                                        #{{ $lot->source_reference ?? $lot->id }}</div>
                                </div>
                                <span
                                    class="badge bg-danger-50 text-danger-600 text-xs">{{ number_format($lot->quantity_kg, 2) }}
                                    {{ __('entities.dashboard.kg') }}</span>
                            </div>
                        @empty
                            <div class="p-20 text-center text-neutral-400 text-sm">
                                {{ __('entities.dashboard.all_stock_sufficient') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <!-- End Low Stock Detail -->

            <!-- Pending Deliveries -->
            <div class="col-12 col-lg-6">
                <div class="card p-20 radius-12 border-0 shadow-xs">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-lg mb-0">{{ __('entities.dashboard.total') }}
                            {{ __('entities.dashboard.pending_deliveries') }}</h6>
                        <span
                            class="bg-neutral-100 text-neutral-600 text-xs px-2 py-1">{{ $metrics['pendingDeliveryCount'] }}
                            {{ __('entities.dashboard.total') }}</span>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        @forelse($metrics['pendingDeliveries'] as $delivery)
                            <div
                                class="p-12 radius-8 border border-neutral-200 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-neutral-900 text-sm">{{ $delivery->reference }}</div>
                                    <div class="text-xs text-neutral-500 mt-1">
                                        {{ number_format($delivery->total_amount, 2) }} AED</div>
                                </div>
                                <span
                                    class="badge bg-warning-50 text-warning-700 text-xs px-2 py-1">{{ __('entities.dashboard.pending') }}</span>
                            </div>
                        @empty
                            <div class="p-20 text-center text-neutral-400 text-sm">
                                {{ __('entities.dashboard.no_pending_deliveries') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <!-- End Pending Deliveries -->
        </div>
        <!-- End Tier 4: Low Stock Detail + Pending Deliveries -->

        {{-- Tier 5: Today's Protocol & Sales --}}
        <div class="row g-3">
            <!-- Today's Protocol -->
            <div class="col-12 col-lg-6">
                <div class="card p-20 radius-12 border-0 shadow-xs">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-lg mb-0">{{ __('entities.dashboard.todays_protocol') }}</h6>
                        <span
                            class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">{{ __('entities.dashboard.today') }}</span>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        @forelse($events as $event)
                            <div
                                class="p-12 radius-8 border border-neutral-200 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-neutral-900 text-sm">
                                        {{ $event->title ?? ($event->name ?? __('Untitled Event')) }}</div>
                                    <div class="text-xs text-neutral-500 mt-1">{{ $event->start_time ?? '' }}</div>
                                </div>
                                <span
                                    class="badge bg-primary-50 text-primary-600 text-xs">{{ ucfirst($event->status ?? __('entities.dashboard.scheduled')) }}</span>
                            </div>
                        @empty
                            <div class="p-20 text-center text-neutral-400 text-sm">
                                {{ __('entities.dashboard.no_events_today') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <!-- End Today's Protocol -->

            <!-- Today's Sales -->
            <div class="col-12 col-lg-6">
                <div class="card p-20 radius-12 border-0 shadow-xs">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-lg mb-0">{{ __('entities.dashboard.todays_sales') }}</h6>
                        <span
                            class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">{{ __('entities.dashboard.today') }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table basic-table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-xs text-uppercase text-neutral-600 fw-semibold w-1">
                                        {{ __('entities.dashboard.avatar') }}
                                    </th>
                                    <th scope="col" class="text-xs text-uppercase text-neutral-600 fw-semibold">
                                        {{ __('entities.dashboard.product') }}
                                    </th>
                                    <th scope="col"
                                        class="text-xs text-uppercase text-neutral-600 fw-semibold text-end">
                                        {{ __('entities.dashboard.quantity') }}
                                    </th>
                                    <th scope="col"
                                        class="text-xs text-uppercase text-neutral-600 fw-semibold text-end">
                                        {{ __('entities.dashboard.amount') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todaysSales as $sale)
                                    <tr>
                                        <td class="align-middle">
                                            <div class="avatar avatar-xs me-2">
                                                @if ($sale->product->image && file_exists(public_path('assets/images/products/' . $sale->product->image)))
                                                    <img src="{{ asset('assets/images/products/' . $sale->product->image) }}"
                                                        alt="{{ $sale->product->name }}"
                                                        class="avatar-img rounded-circle">
                                                @else
                                                    <i class="ri-package-2-line text-muted"></i>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="align-middle fw-medium">
                                            <a href="{{ route('products.show', ['product' => $sale->product->id]) }}"
                                                class="text-neutral-900 hover-text-primary">
                                                {{ $sale->product->name }}
                                            </a>
                                            @if($sale->product->business_name)
                                                <br>
                                                <small class="text-neutral-500">{{ $sale->product->business_name }}</small>
                                            @endif
                                        </td>
                                        <td class="align-middle text-end fw-medium">
                                            {{ number_format($sale->line_total, 2) }} AED
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-neutral-500 py-4">

                                            <i class="streamline-freehand:shopping-bag-sad" style="font-size: 2rem;"></i>
                                            &nbsp;
                                            {{ __('entities.dashboard.no_sales_today') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End Today's Sales -->
        </div>
        <!-- End Tier 5: Today's Protocol & Sales -->
    </div>
    <!-- End Main Container -->
@endsection
