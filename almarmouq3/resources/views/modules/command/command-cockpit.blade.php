@extends('layout.layout')

@php
    $title = __('entities.dashboard.title');
    $subTitle = __('entities.sidebar.dashboard_overview');

    $currencySymbol = $currency->symbol ?? 'AED';
    $revenueLabels = json_encode($charts['revenue_labels'] ?? []);
    $revenueData = json_encode($charts['revenue_data'] ?? []);
    $channelLabels = json_encode($charts['channel_labels'] ?? []);
    $channelData = json_encode($charts['channel_data'] ?? []);

    ob_start();
@endphp
<script>
    var currencySymbol = {{ json_encode($currencySymbol) }};
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
            name: "Revenue",
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
                        return currencySymbol + " " + val.toLocaleString("en-US");
                    }
                }
            }
    };

    var revenueChart = new ApexCharts(document.querySelector("#chart-revenue-velocity"), revenueOptions);
    revenueChart.render();

    var channelColors = ["var(--success-600)", "var(--warning-600)", "var(--info-600)", "var(--danger-600)"];
    var rawChannelData = {!! $channelData !!};
    var rawChannelLabels = {!! $channelLabels !!};
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

            <div class="d-flex align-items-center justify-content-between mb-24">
                <div>
                    <div class="fw-bold text-neutral-900 text-lg mb-1">{{ __('entities.dashboard.title') }}</div>
                    <div class="text-neutral-500 text-sm">{{ __('entities.dashboard.business_glance') }}</div>
                </div>
                <span class="bg-neutral-100 text-neutral-600 text-xs px-2 py-1">
                    <iconify-icon icon="solar:shield-check-outline" class="me-1 align-middle"></iconify-icon>
                    {{ __('entities.dashboard.secure_session') }}
                </span>
            </div>

            <div class="row row-cols-xxxl-4 row-cols-lg-2 row-cols-1 gy-4">
                <div class="col">
                    <div class="card shadow-none border bg-gradient-start-1 h-100">
                        <div class="card-body p-20">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <p class="fw-medium text-primary-light mb-1">{{ __('entities.dashboard.revenue_this_month') }}</p>
                                    <h6 class="mb-0">{{ $currency->symbol }} <small>{{ number_format($metrics['revenue'], 0, '.', ',') }}</small></h6>
                                </div>
                                <div class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                                    <iconify-icon icon="solar:wallet-bold" class="text-white text-2xl mb-0"></iconify-icon>
                                </div>
                            </div>
                            <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center gap-1 {{ $metrics['trend'] >= 0 ? 'text-success-main' : 'text-danger-main' }}">
                                    <iconify-icon icon="{{ $metrics['trend'] >= 0 ? 'bxs:up-arrow' : 'bxs:down-arrow' }}" class="text-xs"></iconify-icon>
                                    {{ abs($metrics['trend']) }}%
                                </span>
                                {{ __('entities.dashboard.vs_last_month') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-none border bg-gradient-start-2 h-100">
                        <div class="card-body p-20">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <p class="fw-medium text-primary-light mb-1">{{ __('entities.dashboard.active_events') }}</p>
                                    <h6 class="mb-0">{{ $metrics['activeEvents'] }}</h6>
                                </div>
                                <div class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                                    <iconify-icon icon="fluent:calendar-24-filled" class="text-white text-2xl mb-0"></iconify-icon>
                                </div>
                            </div>
                            <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center gap-1 text-success-main">
                                    <iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon>
                                    {{ $metrics['pendingApprovals'] }}
                                </span>
                                {{ __('entities.dashboard.upcoming') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-none border bg-gradient-start-3 h-100">
                        <div class="card-body p-20">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <p class="fw-medium text-primary-light mb-1">{{ __('entities.dashboard.pending_delegations') }}</p>
                                    <h6 class="mb-0">{{ $metrics['pendingDelegations'] }}</h6>
                                </div>
                                <div class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                                    <iconify-icon icon="fluent:people-20-filled" class="text-white text-2xl mb-0"></iconify-icon>
                                </div>
                            </div>
                            <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center gap-1 {{ $metrics['pendingApprovals'] > 0 ? 'text-warning-main' : 'text-success-main' }}">
                                    <iconify-icon icon="{{ $metrics['pendingApprovals'] > 0 ? 'bxs:down-arrow' : 'bxs:up-arrow' }}" class="text-xs"></iconify-icon>
                                    {{ $metrics['pendingApprovals'] }}
                                </span>
                                {{ __('entities.dashboard.pending_approvals') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-none border bg-gradient-start-4 h-100">
                        <div class="card-body p-20">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <p class="fw-medium text-primary-light mb-1">{{ __('entities.dashboard.pending_approvals') }}</p>
                                    <h6 class="mb-0">{{ $metrics['pendingApprovals'] }}</h6>
                                </div>
                                <div class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                                    <iconify-icon icon="solar:check-circle-outline" class="text-white text-2xl mb-0"></iconify-icon>
                                </div>
                            </div>
                            <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                                <span class="d-inline-flex align-items-center gap-1 {{ $metrics['pendingApprovals'] > 0 ? 'text-warning-main' : 'text-success-main' }}">
                                    <iconify-icon icon="{{ $metrics['pendingApprovals'] > 0 ? 'bxs:down-arrow' : 'bxs:up-arrow' }}" class="text-xs"></iconify-icon>
                                    @if($metrics['pendingApprovals'] > 0)
                                        {{ $metrics['pendingApprovals'] }}
                                    @else
                                        {{ __('entities.shared.no_data') }}
                                    @endif
                                </span>
                                {{ __('entities.dashboard.awaiting_signoff') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row gy-4 mt-1">
                <div class="col-12 col-lg-8">
                    <div class="card h-100">
                        <div class="card-body p-20">
                            <div class="d-flex align-items-center justify-content-between mb-16">
                                <h6 class="text-lg mb-0">{{ __('entities.dashboard.revenue_velocity') }}</h6>
                                <span class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">{{ __('entities.dashboard.this_month') }}</span>
                            </div>
                            <div id="chart-revenue-velocity" class="pt-28 apexcharts-tooltip-style-1" style="min-height: 280px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="card h-100">
                        <div class="card-body p-20">
                            <div class="d-flex align-items-center justify-content-between mb-16">
                                <h6 class="text-lg mb-0">{{ __('entities.dashboard.channel_mix') }}</h6>
                                <span class="badge bg-neutral-100 text-neutral-600 text-xs px-2 py-1">{{ __('entities.dashboard.all_time') }}</span>
                            </div>
                            <div id="chart-channel-mix" class="apexcharts-tooltip-z-none" style="min-height: 280px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row gy-4 mt-1">
                <div class="col-12">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <h6 class="text-lg mb-0">{{ __('entities.dashboard.todays_agenda') }}</h6>
                                <a href="{{ route('command.agenda') }}" class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                                    {{ __('entities.dashboard.view_all') }}
                                    <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                                </a>
                            </div>

                            <div class="table-responsive scroll-sm mt-3">
                                <table class="table basic-table mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-xs text-uppercase text-neutral-600 fw-semibold">
                                                {{ __('entities.dashboard.event') }}
                                            </th>
                                            <th scope="col" class="text-xs text-uppercase text-neutral-600 fw-semibold">
                                                {{ __('entities.dashboard.start_time') }}
                                            </th>
                                            <th scope="col" class="text-xs text-uppercase text-neutral-600 fw-semibold">
                                                {{ __('entities.dashboard.location') }}
                                            </th>
                                            <th scope="col" class="text-xs text-uppercase text-neutral-600 fw-semibold text-center">
                                                {{ __('entities.dashboard.status') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($todaysAgenda as $event)
                                            <tr>
                                                <td class="align-middle fw-medium">
                                                    {{ $event->displayName() ?? ($event->title ?? 'Untitled Event') }}
                                                </td>
                                                <td class="align-middle">
                                                    {{ $event->start_time ? $event->start_time->format('g:i A') : '-' }}
                                                </td>
                                                <td class="align-middle text-secondary-light">
                                                    {{ $event->displayLocation() ?? '-' }}
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="badge bg-primary-50 text-primary-600 text-xs px-2 py-1">
                                                        {{ $event->statusLabel() }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-neutral-400 py-4">
                                                    <i class="streamline-freehand:calendar-search" style="font-size: 2rem;"></i>
                                                    &nbsp; {{ __('entities.dashboard.no_events_today') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

@endsection
