<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Delegation;
use App\Models\Event;
use App\Models\ProductLot;
use App\Models\Sale;
use App\Models\Setting;
use Carbon\Carbon;

class FinancialController extends Controller
{
    public function index()
    {
        $currency = Setting::currency() ?? Currency::default();
        $today = Carbon::today();
        $monthStart = now()->startOfMonth();
        $yearStart = now()->startOfYear();

        $mtdRevenue = (float) Sale::where('sold_at', '>=', $monthStart)->sum('total_amount');
        $ytdRevenue = (float) Sale::where('sold_at', '>=', $yearStart)->sum('total_amount');

        $cogs = (float) ProductLot::sum('supplier_amount');
        $grossMargin = $ytdRevenue - $cogs;
        $grossMarginPct = $ytdRevenue > 0 ? round(($grossMargin / $ytdRevenue) * 100, 1) : 0;

        $marginThreshold = (float) Setting::get('financial.margin_threshold', 60);

        $vatCollected = (float) Sale::where('sold_at', '>=', $yearStart)->sum('vat_amount');
        $vatPaid = 350000;
        $netVatPayable = $vatCollected - $vatPaid;

        $arAging = Sale::leftJoin('customers', 'sales.customer_id', '=', 'customers.id')
            ->whereIn('sales.status', ['processing', 'pending', 'shipped'])
            ->where('sales.sold_at', '>=', now()->subMonths(3))
            ->select([
                'sales.id', 'sales.reference', 'sales.total_amount', 'sales.sold_at',
                'sales.status', 'customers.name as customer_name', 'customers.type as customer_type',
            ])
            ->orderBy('sales.sold_at', 'asc')
            ->limit(20)
            ->get();

        $overdueAr = (float) Sale::whereIn('status', ['pending'])
            ->where('sold_at', '<', now()->subDays(30))
            ->sum('total_amount');

        $lockedFunds = 0;
        $upcomingOutflows = collect();

        $lockedFunds = ProductLot::where('source_date', '>=', now()->startOfMonth())
            ->where('source_date', '<', now()->addDays(7))
            ->sum('supplier_amount');

        $upcomingOutflows = ProductLot::where('source_date', '>=', now())
            ->where('source_date', '<', now()->addDays(14))
            ->orderBy('source_date')
            ->limit(10)
            ->get();

        $availableCash = (float) Setting::get('financial.available_cash', 2500000);
        $lockedFundsTotal = (float) $lockedFunds;

        $marginAlert = $grossMarginPct < $marginThreshold;

        $todayEventCount = Event::whereDate('start_time', $today)
            ->whereIn('type', ['meeting', 'showcase', 'testing'])
            ->count();
        $todayDelegationCount = Delegation::where('assignee_id', auth()->id())
            ->whereDate('due_date', $today)
            ->whereIn('status', ['not_started', 'in_progress', 'blocked'])
            ->count();

        $prevMonthStart = now()->copy()->subMonth()->startOfMonth();
        $prevMonthEnd = now()->copy()->subMonth()->endOfMonth();
        $prevYtdStart = now()->copy()->subYear()->startOfYear();
        $prevYtdEnd = now()->copy()->subYear()->endOfYear();
        $prevMtdRevenue = (float) Sale::whereBetween('sold_at', [$prevMonthStart, $prevMonthEnd])->sum('total_amount');
        $prevYtdRevenue = (float) Sale::whereBetween('sold_at', [$prevYtdStart, $prevYtdEnd])->sum('total_amount');
        $prevCogs = (float) ProductLot::whereBetween('source_date', [$prevMonthStart, $prevMonthEnd])->sum('supplier_amount');
        $totalCogs = (float) ProductLot::where('source_date', '>=', $yearStart)->sum('supplier_amount');

        $chartLabels = [];
        $chartIncome = [];
        $chartExpenses = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->copy()->subDays($i);
            $chartLabels[] = $day->format('D');
            $chartIncome[] = round((float) Sale::whereDate('sold_at', $day->toDateString())->sum('total_amount') / 1000, 2);
            $chartExpenses[] = round((float) ProductLot::whereDate('source_date', $day->toDateString())->sum('supplier_amount') / 1000, 2);
        }

        $donutData = [
            round($prevYtdRevenue > 0 ? ($prevYtdRevenue / $ytdRevenue * 100) : 0, 1),
            round($ytdRevenue > 0 ? (($ytdRevenue - $prevYtdRevenue) / $ytdRevenue * 100) : 0, 1),
            round($totalCogs > 0 ? ($prevCogs / $totalCogs * 100) : 0, 1),
            $grossMarginPct,
        ];

        $barLabels = [];
        $barRevenue = [];
        $barCogs = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->copy()->subDays($i);
            $barLabels[] = $day->format('D');
            $barRevenue[] = round((float) Sale::whereDate('sold_at', $day->toDateString())->sum('total_amount') / 1000, 2);
            $barCogs[] = round((float) ProductLot::whereDate('source_date', $day->toDateString())->sum('supplier_amount') / 1000, 2);
        }

        $nextFilingDate = now()->startOfYear()->addMonths(3)->addDays(10);
        $daysUntilFiling = now()->diffInDays($nextFilingDate);

        $marginPct = $grossMarginPct;

        return view('modules.financial.financial-cockpit', [
            'currency' => $currency,
            'today' => $today,
            'metrics' => [
                'available_cash' => $availableCash,
                'locked_funds' => $lockedFundsTotal,
                'upcoming_outflows_total' => (float) $upcomingOutflows->sum('supplier_amount'),
                'mtd_revenue' => $mtdRevenue,
                'ytd_revenue' => $ytdRevenue,
                'ytd_vs_ly_pct' => $prevYtdRevenue > 0 ? round((($ytdRevenue - $prevYtdRevenue) / $prevYtdRevenue) * 100, 1) : 0,
                'mtd_vs_prev_pct' => $prevMtdRevenue > 0 ? round((($mtdRevenue - $prevMtdRevenue) / $prevMtdRevenue) * 100, 1) : 0,
                'cogs_vs_prev_pct' => $prevCogs > 0 ? round((($cogs - $prevCogs) / $prevCogs) * 100, 1) : 0,
                'gross_margin' => $grossMargin,
                'gross_margin_pct' => $grossMarginPct,
                'margin_pct' => $marginPct,
                'total_cogs' => $totalCogs,
                'margin_threshold' => $marginThreshold,
                'margin_alert' => $marginAlert,
                'vat_collected' => $vatCollected,
                'vat_paid' => $vatPaid,
                'net_vat_payable' => $netVatPayable,
                'ar_total' => (float) Sale::whereIn('status', ['processing', 'pending', 'shipped'])->sum('total_amount'),
                'ar_overdue' => $overdueAr,
                'chart_labels' => $chartLabels,
                'chart_income' => $chartIncome,
                'chart_expenses' => $chartExpenses,
                'donut_data' => $donutData,
                'bar_revenue' => $barRevenue,
                'bar_cogs' => $barCogs,
                'bar_labels' => $barLabels,
                'days_until_filing' => $daysUntilFiling,
            ],
            'ar_aging' => $arAging,
            'upcoming_outflows' => $upcomingOutflows,
            'today_event_count' => $todayEventCount,
            'today_delegation_count' => $todayDelegationCount,
        ]);
    }

    public function pnl()
    {
        $sales = Sale::where('sold_at', '>=', now()->startOfYear())
            ->with('items.product')
            ->orderBy('sold_at', 'desc')
            ->get();

        $quarterlyRevenue = [];
        for ($q = 1; $q <= 4; $q++) {
            $start = now()->startOfYear()->addMonths(($q - 1) * 3);
            $end = $q === 4 ? now() : $start->copy()->addMonths(3)->subDay();
            $quarterlyRevenue[$q] = (float) Sale::whereBetween('sold_at', [$start, $end])->sum('total_amount');
        }

        $cogsByQuarter = [];
        for ($q = 1; $q <= 4; $q++) {
            $start = now()->startOfYear()->addMonths(($q - 1) * 3);
            $end = $q === 4 ? now() : $start->copy()->addMonths(3)->subDay();
            $cogsByQuarter[$q] = (float) ProductLot::whereBetween('source_date', [$start, $end])->sum('supplier_amount');
        }

        $revenueQ1 = $quarterlyRevenue[1] ?? 0;
        $revenueQ2 = $quarterlyRevenue[2] ?? 0;
        $revenueQ3 = $quarterlyRevenue[3] ?? 0;
        $revenueQ4 = $quarterlyRevenue[4] ?? 0;
        $revenueYTD = array_sum($quarterlyRevenue);

        $cogsQ1 = $cogsByQuarter[1] ?? 0;
        $cogsQ2 = $cogsByQuarter[2] ?? 0;
        $cogsQ3 = $cogsByQuarter[3] ?? 0;
        $cogsQ4 = $cogsByQuarter[4] ?? 0;
        $totalCogs = array_sum($cogsByQuarter);

        $grossMargin = $revenueYTD - $totalCogs;
        $grossMarginPct = $revenueYTD > 0 ? round(($grossMargin / $revenueYTD) * 100, 1) : 0;

        $opex = (float) Setting::get('financial.monthly_opex', 680000);
        $noi = $grossMargin - $opex;
        $netMarginPct = $revenueYTD > 0 ? round(($noi / $revenueYTD) * 100, 1) : 0;

        $marginThreshold = (float) Setting::get('financial.margin_threshold', 60);
        $marginAlert = $grossMarginPct < $marginThreshold;

        $segmentRevenue = [
            'sovereign' => (float) Sale::whereHas('customer', fn ($q) => $q->whereIn('type', ['sovereign', 'b2b', 'corporate']))->sum('total_amount'),
            'vip_private' => (float) Sale::whereHas('customer', fn ($q) => $q->whereIn('type', ['private']))->sum('total_amount'),
            'retail' => (float) Sale::whereDoesntHave('customer')->orWhereHas('customer', fn ($q) => $q->whereNotIn('type', ['sovereign', 'b2b', 'corporate', 'private']))->sum('total_amount'),
        ];

        return view('modules.financial.pnl', [
            'sales' => $sales,
            'userId' => auth()->id(),
            'quarterly_revenue' => $quarterlyRevenue,
            'quarterly_cogs' => $cogsByQuarter,
            'segment_revenue' => $segmentRevenue,
            'metrics' => [
                'revenue_ytd' => $revenueYTD,
                'revenue_q1' => $revenueQ1,
                'revenue_q2' => $revenueQ2,
                'revenue_q3' => $revenueQ3,
                'revenue_q4' => $revenueQ4,
                'total_cogs' => $totalCogs,
                'cogs_q1' => $cogsQ1,
                'cogs_q2' => $cogsQ2,
                'cogs_q3' => $cogsQ3,
                'cogs_q4' => $cogsQ4,
                'gross_margin' => $grossMargin,
                'gross_margin_pct' => $grossMarginPct,
                'margin_threshold' => $marginThreshold,
                'margin_alert' => $marginAlert,
                'opex' => $opex,
                'net_profit' => $noi,
                'net_margin_pct' => $netMarginPct,
            ],
        ]);
    }

    public function expenses()
    {
        $expenses = ProductLot::orderBy('source_date', 'desc')
            ->with('product')
            ->limit(50)
            ->get();

        $totalExpenses = ProductLot::sum('supplier_amount');

        $expenseByCategory = [
            'raw_materials' => (float) ProductLot::where('grade', 'like', '%raw%')->sum('supplier_amount'),
            'bespoke_assembly' => (float) ProductLot::where('grade', 'like', '%bespoke%')->sum('supplier_amount'),
            'logistics' => (float) ProductLot::where('distributor_shape', 'like', '%logistics%')->sum('supplier_amount'),
            'certification' => (float) ProductLot::sum('working_cost_rate'),
        ];

        $weeklyExpenses = [];
        for ($i = 0; $i < 8; $i++) {
            $start = now()->subWeeks($i)->startOfWeek();
            $end = now()->subWeeks($i)->endOfWeek();
            $weeklyExpenses[] = [
                'label' => $start->format('M d'),
                'amount' => (float) ProductLot::whereBetween('source_date', [$start, $end])->sum('supplier_amount'),
            ];
        }
        $weeklyExpenses = array_reverse($weeklyExpenses);

        $avgUnitCost = 0;
        $totalKg = (float) ProductLot::sum('quantity_kg');
        if ($totalKg > 0) {
            $avgUnitCost = $totalExpenses / $totalKg;
        }

        return view('modules.financial.expenses', [
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
            'userId' => auth()->id(),
            'expense_by_category' => $expenseByCategory,
            'weekly_expenses' => $weeklyExpenses,
            'avg_unit_cost' => $avgUnitCost,
        ]);
    }

    public function tax()
    {
        $sales = Sale::where('sold_at', '>=', now()->startOfYear())
            ->orderBy('sold_at', 'desc')
            ->limit(50)
            ->get();

        $vatCollected = (float) Sale::where('sold_at', '>=', now()->startOfYear())->sum('vat_amount');
        $vatPaid = 350000;
        $netVatPayable = $vatCollected - $vatPaid;

        $taxExemptInvoices = Sale::where('status', 'completed')
            ->where(function ($query) {
                $query->whereJsonContains('payload->tax_exempt', true)
                    ->orWhereIn('customer_id', function ($sub) {
                        $sub->select('id')->from('customers')->whereIn('type', ['sovereign']);
                    });
            })
            ->get();

        $vatByMonth = [];
        for ($m = 1; $m <= now()->month; $m++) {
            $monthStart = now()->startOfYear()->addMonths($m - 1)->startOfMonth();
            $monthEnd = now()->startOfYear()->addMonths($m - 1)->endOfMonth();
            $vatByMonth[] = [
                'month' => $monthStart->format('M'),
                'collected' => (float) Sale::whereBetween('sold_at', [$monthStart, $monthEnd])->sum('vat_amount'),
            ];
        }

        $nextFilingDate = now()->startOfYear()->addMonths(3)->addDays(10);

        return view('modules.financial.tax', [
            'sales' => $sales,
            'vat_collected' => $vatCollected,
            'vat_paid' => $vatPaid,
            'net_vat_payable' => $netVatPayable,
            'tax_exempt_invoices' => $taxExemptInvoices,
            'vat_by_month' => $vatByMonth,
            'next_filing_date' => $nextFilingDate,
            'days_until_filing' => now()->diffInDays($nextFilingDate),
            'userId' => auth()->id(),
        ]);
    }
}
