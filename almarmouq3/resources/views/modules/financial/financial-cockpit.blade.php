@extends('layout.layout')

@php
    $title = __('entities.financial.overview.available_cash');
    $subTitle = __('entities.financial.overview.available_cash');
    $daysUntilFiling = $metrics['days_until_filing'] ?? 75;
@endphp

@section('content')

    <div class="row gy-4">
        <div class="col-12">
            <div class="card radius-12">
                <div class="card-body p-16">
                    <div class="row gy-4">

                        <div class="col-xxl-3 col-xl-4 col-sm-6">
                            <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-1 left-line line-bg-primary position-relative overflow-hidden">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-md">{{ __('entities.financial.overview.available_cash') }}</span>
                                        <h6 class="fw-semibold mb-1">{{ $currency->symbol }} <small class="text-2xl">{{ number_format($metrics['available_cash'], 0, '.', ',') }}</small></h6>
                                    </div>
                                    <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-primary-100 text-primary-600">
                                        <i class="ri-wallet-3-line"></i>
                                    </span>
                                </div>
                                <p class="text-sm mb-0"><span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm"><i class="ri-arrow-right-up-line"></i> {{ __('entities.financial.overview.immediately_available') }}</span> {{ __('entities.shared.ok') }} </p>
                            </div>
                        </div>

                        <div class="col-xxl-3 col-xl-4 col-sm-6">
                            <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-2 left-line line-bg-lilac position-relative overflow-hidden">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-md">{{ __('entities.financial.overview.gross_sales') }}</span>
                                        <h6 class="fw-semibold mb-1">{{ $currency->symbol }} <small class="text-2xl">{{ number_format($metrics['ytd_revenue'], 0, '.', ',') }}</small></h6>
                                    </div>
                                    <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-lilac-200 text-lilac-600">
                                        <i class="ri-shopping-cart-fill"></i>
                                    </span>
                                </div>
                                <p class="text-sm mb-0"><span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm"><i class="ri-arrow-right-up-line"></i> {{ $metrics['ytd_vs_ly_pct'] }}%</span> {{ __('entities.financial.pnl.metrics.vs_last_period') }} </p>
                            </div>
                        </div>

                        <div class="col-xxl-3 col-xl-4 col-sm-6">
                            <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-3 left-line line-bg-success position-relative overflow-hidden">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-md">{{ __('entities.financial.overview.mtd_revenue') }}</span>
                                        <h6 class="fw-semibold mb-1">{{ $currency->symbol }} <small class="text-2xl">{{ number_format($metrics['mtd_revenue'], 0, '.', ',') }}</small></h6>
                                    </div>
                                    <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-success-200 text-success-600">
                                        <i class="ri-money-dollar-circle-line"></i>
                                    </span>
                                </div>
                                <p class="text-sm mb-0"><span class="bg-danger-focus px-1 rounded-2 fw-medium text-danger-main text-sm"><i class="ri-arrow-right-down-line"></i> {{ $metrics['mtd_vs_prev_pct'] }}%</span> {{ __('entities.financial.pnl.metrics.vs_last_period') }} </p>
                            </div>
                        </div>

                        <div class="col-xxl-3 col-xl-4 col-sm-6">
                            <div class="px-20 py-16 shadow-none radius-8 h-100 gradient-deep-4 left-line line-bg-warning position-relative overflow-hidden">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-md">{{ __('entities.financial.overview.total_expense') }}</span>
                                        <h6 class="fw-semibold mb-1">{{ $currency->symbol }} <small class="text-2xl">{{ number_format($metrics['total_cogs'], 0, '.', ',') }}</small></h6>
                                    </div>
                                    <span class="w-44-px h-44-px radius-8 d-inline-flex justify-content-center align-items-center text-2xl mb-12 bg-warning-focus text-warning-600">
                                        <i class="ri-store-2-line"></i>
                                    </span>
                                </div>
                                <p class="text-sm mb-0">
                                    @if($metrics['margin_pct'] < $metrics['margin_threshold'])
                                        <span class="bg-danger-focus px-1 rounded-2 fw-medium text-danger-main text-sm"><i class="ri-alert-line"></i> {{ __('entities.financial.overview.alert_threshold', ['threshold' => $metrics['margin_threshold']]) }}</span>
                                    @else
                                        <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm"><i class="ri-arrow-right-up-line"></i> {{ $metrics['margin_pct'] }}% {{ __('entities.financial.pnl.metrics.of_revenue') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-8">
            <div class="card h-100">
                <div class="card-body p-24 mb-8">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">{{ __('entities.financial.overview.revenue_velocity') }}</h6>
                        <select class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                            <option>{{ __('entities.financial.pnl.filter.ytd') }}</option>
                            <option>{{ __('entities.financial.pnl.filter.quarterly') }}</option>
                            <option>{{ __('entities.financial.pnl.filter.monthly') }}</option>
                        </select>
                    </div>
                    <ul class="d-flex flex-wrap align-items-center justify-content-center my-3 gap-24">
                        <li class="d-flex flex-column gap-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="w-8-px h-8-px rounded-pill bg-primary-600"></span>
                                <span class="text-secondary-light text-sm fw-semibold">{{ __('entities.financial.pnl.metrics.revenue') }} </span>
                            </div>
                            <div class="d-flex align-items-center gap-8">
                                <h6 class="mb-0">{{ $currency->symbol }} <small class="fw-semibold">{{ number_format($metrics['mtd_revenue'], 0, '.', ',') }}</small></h6>
                                <span class="text-success-600 d-flex align-items-center gap-1 text-sm fw-bolder">
                                    {{ $metrics['mtd_vs_prev_pct'] }}%
                                    <i class="ri-arrow-up-s-fill d-flex"></i>
                                </span>
                            </div>
                        </li>
                        <li class="d-flex flex-column gap-1">
                            <div class="d-flex align-items-center gap-2">
                                <span class="w-8-px h-8-px rounded-pill bg-warning-600"></span>
                                <span class="text-secondary-light text-sm fw-semibold">{{ __('entities.financial.pnl.metrics.total_cogs') }} </span>
                            </div>
                            <div class="d-flex align-items-center gap-8">
                                <h6 class="mb-0">{{ $currency->symbol }} <small class="fw-semibold">{{ number_format($metrics['total_cogs'], 0, '.', ',') }}</small></h6>
                                <span class="text-danger-600 d-flex align-items-center gap-1 text-sm fw-bolder">
                                    {{ $metrics['cogs_vs_prev_pct'] }}%
                                    <i class="ri-arrow-down-s-fill d-flex"></i>
                                </span>
                            </div>
                        </li>
                    </ul>
                    <div id="incomeExpense" class="apexcharts-tooltip-style-1"></div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-md-6">
            <div class="card h-100">
                <div class="card-body p-24">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">{{ __('entities.financial.pnl.margin_gauge_title') }}</h6>
                        <span class="badge bg-{{ $metrics['margin_pct'] < $metrics['margin_threshold'] ? 'danger' : 'success' }}-focus text-{{ $metrics['margin_pct'] < $metrics['margin_threshold'] ? 'danger' : 'success' }}-main">
                            {{ $metrics['margin_pct'] }}%
                        </span>
                    </div>
                    <p class="text-sm text-secondary-light mb-0 mt-8">
                        {{ __('entities.financial.pnl.metrics.gross_margin') }} {{ $metrics['gross_margin_pct'] }}% {{ __('entities.financial.pnl.metrics.of_revenue') }}.
                        @if($metrics['margin_alert'])
                            {{ __('entities.financial.overview.alert_threshold', ['threshold' => $metrics['margin_threshold']]) }}
                        @else
                            {{ __('entities.financial.pnl.metrics.margin_threshold', ['threshold' => $metrics['margin_threshold']]) }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-md-6">
            <div class="card h-100">
                <div class="card-body p-24">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">{{ __('entities.financial.overview.ar_aging') }}</h6>
                        <a href="{{ route('financial.pnl') }}" class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                            {{ __('entities.shared.view_all') }}
                            <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                        </a>
                    </div>

                    <div class="mt-3">
                        <div class="d-flex flex-column gap-2 mb-16">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-secondary-light text-sm fw-semibold">{{ __('entities.financial.overview.total_ar') }}</span>
                                <span class="fw-semibold">{{ $currency->symbol }} <small>{{ number_format($metrics['ar_total'], 0, '.', ',') }}</small></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="text-danger-light text-sm fw-semibold">{{ __('entities.financial.overview.overdue_ar') }}</span>
                                <span class="fw-semibold text-danger-main">{{ $currency->symbol }} <small>{{ number_format($metrics['ar_overdue'], 0, '.', ',') }}</small></span>
                            </div>
                        </div>

                        @if($ar_aging->isNotEmpty())
                        <div class="table-responsive scroll-sm mt-3">
                            <table class="table basic-table mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('entities.shared.customer') }}</th>
                                        <th scope="col">{{ __('entities.shared.reference') }}</th>
                                        <th scope="col">{{ __('entities.shared.amount') }}</th>
                                        <th scope="col">{{ __('entities.shared.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($ar_aging as $invoice)
                                    <tr>
                                        <td>
                                            <div class="flex-grow-1">
                                                <h6 class="text-md mb-0">{{ $invoice->customer_name }}</h6>
                                                <span class="text-sm text-secondary-light">{{ $invoice->reference }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $currency->symbol }} <small>{{ number_format($invoice->total_amount, 0, '.', ',') }}</small></td>
                                        <td>
                                            <span class="px-8 py-2 rounded-pill fw-medium text-xs {{ $invoice->status === 'pending' ? 'bg-warning-focus text-warning-main' : 'bg-success-focus text-success-main' }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                            <p class="text-sm text-secondary-light mt-3">{{ __('entities.financial.overview.no_outstanding_receivables') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-md-6">
            <div class="card h-100">
                <div class="card-body p-24">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">{{ __('entities.financial.overview.upcoming_outflows_title') }}</h6>
                        <a href="{{ route('financial.expenses') }}" class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                            {{ __('entities.shared.view_all') }}
                            <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                        </a>
                    </div>
                    <ul class="d-flex flex-column gap-2 mt-3">
                        <li class="d-flex align-items-center justify-content-between">
                            <span class="text-secondary-light text-sm fw-semibold">{{ __('entities.shared.total') }}</span>
                            <span class="fw-semibold">{{ $currency->symbol }} <small>{{ number_format($metrics['upcoming_outflows_total'], 0, '.', ',') }}</small></span>
                        </li>
                        <li class="d-flex align-items-center justify-content-between">
                            <span class="text-secondary-light text-sm fw-semibold">{{ __('entities.financial.overview.locked_funds') }}</span>
                            <span class="fw-semibold">{{ $currency->symbol }} <small>{{ number_format($metrics['locked_funds'], 0, '.', ',') }}</small></span>
                        </li>
                    </ul>
                    @if($upcoming_outflows->isNotEmpty())
                    <div class="table-responsive scroll-sm mt-3">
                        <table class="table basic-table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('entities.financial.expenses.headers.voucher') }}</th>
                                    <th scope="col">{{ __('entities.financial.overview.due_date') }}</th>
                                    <th scope="col">{{ __('entities.shared.amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcoming_outflows as $outflow)
                                <tr>
                                    <td>{{ $outflow->product?->sku ?? $outflow->product_name ?? '—' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($outflow->source_date)->format('M d') }}</td>
                                    <td>{{ $currency->symbol }} <small>{{ number_format($outflow->supplier_amount, 0, '.', ',') }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-md-6">
            <div class="card h-100">
                <div class="card-body p-24">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">{{ __('entities.tax.vat') }}</h6>
                        <span class="badge bg-info-focus text-info-main" id="taxDeadline">
                            {{ $daysUntilFiling }} {{ __('entities.financial.overview.days_left') }}
                        </span>
                    </div>
                    <ul class="d-flex flex-column gap-2 mt-3">
                        <li class="d-flex align-items-center justify-content-between">
                            <span class="text-secondary-light text-sm fw-semibold">{{ __('entities.financial.overview.vat_collected_vs_paid') }}</span>
                            <span class="text-success-main">{{ $currency->symbol }} <small>{{ number_format($metrics['vat_collected'], 0, '.', ',') }}</small></span>
                        </li>
                        <li class="d-flex align-items-center justify-content-between">
                            <span class="text-secondary-light text-sm fw-semibold">{{ __('entities.financial.overview.vat_paid') }}</span>
                            <span class="text-danger-main">{{ $currency->symbol }} <small>{{ number_format($metrics['vat_paid'], 0, '.', ',') }}</small></span>
                        </li>
                        <li class="d-flex align-items-center justify-content-between fw-bold">
                            <span class="text-secondary-light">{{ __('entities.financial.overview.net_vat_payable') }}</span>
                            <span class="{{ $metrics['net_vat_payable'] > 0 ? 'text-warning-main' : 'text-success-main' }}">
                                {{ $currency->symbol }} <small>{{ number_format($metrics['net_vat_payable'], 0, '.', ',') }}</small>
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-xxl-8">
            <div class="card h-100">
                <div class="card-body p-24">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">{{ __('entities.financial.pnl.breakdown_title') }}</h6>
                        <a href="{{ route('financial.tax') }}" class="text-primary-600 hover-text-primary d-flex align-items-center gap-1">
                            {{ __('entities.shared.view_all') }}
                            <iconify-icon icon="solar:alt-arrow-right-linear" class="icon"></iconify-icon>
                        </a>
                    </div>

                    @if($ar_aging->isNotEmpty())
                    <div class="table-responsive scroll-sm mt-3">
                        <table class="table basic-table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('entities.shared.customer') }}</th>
                                    <th scope="col">{{ __('entities.financial.pnl.headers.category') }}</th>
                                    <th scope="col">{{ __('entities.shared.date') }}</th>
                                    <th scope="col" class="text-end">{{ __('entities.shared.amount') }}</th>
                                    <th scope="col">{{ __('entities.shared.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ar_aging as $invoice)
                                <tr>
                                    <td>
                                        <div class="flex-grow-1">
                                            <h6 class="text-md mb-0">{{ $invoice->customer_name }}</h6>
                                            <span class="text-xs text-secondary-light">{{ $invoice->customer_type }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $invoice->reference }}</td>
                                    <td>{{ \Carbon\Carbon::parse($invoice->sold_at)->format('M d') }}</td>
                                    <td class="text-end">{{ $currency->symbol }} <small>{{ number_format($invoice->total_amount, 0, '.', ',') }}</small></td>
                                    <td>
                                        <span class="px-8 py-2 rounded-pill fw-medium text-xs {{ $invoice->status === 'paid' ? 'bg-success-focus text-success-main' : ($invoice->status === 'pending' ? 'bg-warning-focus text-warning-main' : 'bg-danger-focus text-danger-main') }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <p class="text-sm text-secondary-light mt-3">{{ __('entities.financial.overview.no_outstanding_invoices') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-8">
        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                        <h6 class="mb-2 fw-bold text-lg mb-0">{{ __('entities.financial.overview.vat_collected_vs_paid') }}</h6>
                </div>
                <div class="d-flex align-items-center gap-3 flex-wrap mt-3">
                    <div class="col-xxl-6 col-md-6">
                        <div class="card shadow-none border bg-gradient-start-1 h-100">
                            <div class="card-body p-20 text-center">
                                <h6 class="fw-semibold mb-1">{{ __('entities.financial.overview.net_vat_payable') }}</h6>
                                <h4 class="mb-0 {{ $metrics['net_vat_payable'] > 0 ? 'text-warning-main' : 'text-success-main' }}">{{ $currency->symbol }} <small>{{ number_format($metrics['net_vat_payable'], 0, '.', ',') }}</small></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-6 col-md-6">
                        <div class="card shadow-none border bg-gradient-start-2 h-100">
                            <div class="card-body p-20 text-center">
                                <h6 class="fw-semibold mb-1">{{ __('entities.financial.pnl.metrics.gross_margin') }}</h6>
                                <h4 class="mb-0">{{ $metrics['gross_margin_pct'] }}%</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row gy-4 mt-1">
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-body p-24">
                                <h6 class="mb-2 fw-bold text-lg">{{ __('entities.financial.pnl.trend_title') }}</h6>
                                <div id="purchaseSaleChart" class="apexcharts-tooltip-z-none" style="min-height: 260px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="card h-100">
                            <div class="card-body p-24">
                                <h6 class="mb-2 fw-bold text-lg">{{ __('entities.financial.pnl.metrics.gross_margin') }}</h6>
                                <div id="userOverviewDonutChart" class="apexcharts-tooltip-z-none" style="min-height: 260px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    var currencySymbol = @json($currency->symbol ?? 'AED');
    var chartIncome = {!! json_encode($metrics['chart_income'] ?? []) !!};
    var chartExpenses = {!! json_encode($metrics['chart_expenses'] ?? []) !!};
    var chartLabels = {!! json_encode($metrics['chart_labels'] ?? ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']) !!};
    var donutData = {!! json_encode($metrics['donut_data'] ?? [30, 30, 20, 20]) !!};
    var barRevenue = {!! json_encode($metrics['bar_revenue'] ?? []) !!};
    var barCogs = {!! json_encode($metrics['bar_cogs'] ?? []) !!};
    var barLabels = {!! json_encode($metrics['bar_labels'] ?? ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']) !!};

            function createChartTwo(chartId, color1, color2, incomeData, expenseData, labels) {
                var options = {
                    series: [{
                        name: "Income",
                        data: incomeData
                    }, {
                        name: "Expenses",
                        data: expenseData
                    }],
                    legend: { show: false },
                    chart: {
                        type: "area", width: "100%", height: 270,
                        toolbar: { show: false },
                        padding: { left: 0, right: 0, top: 0, bottom: 0 }
                    },
                    dataLabels: { enabled: false },
                    stroke: { curve: "smooth", width: 3, colors: [color1, color2], lineCap: "round" },
                    grid: {
                        show: true, borderColor: "#D1D5DB", strokeDashArray: 1, position: "back",
                        xaxis: { lines: { show: false } },
                        yaxis: { lines: { show: true } },
                        row: { colors: undefined, opacity: 0.5 },
                        column: { colors: undefined, opacity: 0.5 },
                        padding: { top: -20, right: 0, bottom: -10, left: 0 },
                    },
                    fill: {
                        type: "gradient",
                        gradient: {
                            shade: "light", type: "vertical", shadeIntensity: 0.5,
                            gradientToColors: [undefined, color2 + "00"],
                            inverseColors: false,
                            opacityFrom: [0.4, 0.6], opacityTo: [0.3, 0.3],
                            stops: [0, 100],
                        },
                    },
                    markers: {
                        colors: [color1, color2], strokeWidth: 3, size: 0,
                        hover: { size: 10 }
                    },
                    xaxis: {
                        labels: { show: false },
                        categories: labels,
                        tooltip: { enabled: false },
                        labels: {
                            formatter: function(value) { return value; },
                            style: { fontSize: "14px" }
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(value) { return currencySymbol + " " + value.toFixed(2) + "k"; },
                            style: { fontSize: "14px" }
                        }
                    },
                    tooltip: { x: { format: "dd/MM/yy HH:mm" } }
                };
                var chart = new ApexCharts(document.querySelector("#" + chartId), options);
                chart.render();
            }
            createChartTwo("incomeExpense", "#487FFF", "#FF9F29", chartIncome, chartExpenses, chartLabels);

            var donutOptions = {
                series: donutData,
                colors: ["#FF9F29", "#487FFF", "#45B369", "#9935FE"],
                __labels__ __('entities.financial.pnl.segments.purchase') }}", "{{ __('entities.financial.pnl.segments.sales') }}", "{{ __('entities.financial.pnl.segments.expense') }}", "{{ __('entities.financial.pnl.segments.gross_profit') }}"],
                legend: { show: false },
                chart: { type: "donut", height: 270, sparkline: { enabled: true },
                    margin: { top: 0, right: 0, bottom: 0, left: 0 },
                    padding: { top: 0, right: 0, bottom: 0, left: 0 } },
                stroke: { width: 0 },
                dataLabels: { enabled: true },
                responsive: [{ breakpoint: 480, options: { chart: { width: 200 }, legend: { position: "bottom" } } }]
            };
            var donutChart = new ApexCharts(document.querySelector("#userOverviewDonutChart"), donutOptions);
            donutChart.render();

            var barOptions = {
                series: [{
                    name: "{{ __('entities.financial.pnl.segments.revenue') }}", data: barRevenue
                }, {
                    name: "{{ __('entities.financial.pnl.segments.cogs') }}", data: barCogs
                }],
                colors: ["#45B369", "#FF9F29"],
                labels: barLabels,
                legend: { show: false },
                chart: { type: "bar", height: 260, toolbar: { show: false } },
                grid: { show: true, borderColor: "#D1D5DB", strokeDashArray: 4, position: "back" },
                plotOptions: { bar: { borderRadius: 4, columnWidth: 8 } },
                dataLabels: { enabled: false },
                states: { hover: { filter: { type: "none" } } },
                stroke: { show: true, width: 0, colors: ["transparent"] },
                xaxis: { categories: barLabels },
                fill: { opacity: 1, width: 18 }
            };
            var barChart = new ApexCharts(document.querySelector("#purchaseSaleChart"), barOptions);
            barChart.render();
            </script>
@endpush
