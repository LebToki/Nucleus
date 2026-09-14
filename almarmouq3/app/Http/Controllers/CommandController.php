<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\CommunicationLog;
use App\Models\Event;
use App\Models\Delegation;
use App\Models\DelegationStatus;
use App\Models\ProductLot;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CommandController extends Controller
{
    public function index()
    {
        $events = auth()->user()->events ?? collect();

        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $prevMonthStart = $now->copy()->subMonth()->startOfMonth();
        $prevMonthEnd = $now->copy()->subMonth()->endOfMonth();
        $todaysSaleItems = SaleItem::whereHas('sale', function ($query) {
            $query->whereDate('sold_at', today());
        })->with('product')
        ->get();

        $revenueThisMonth = (float) Sale::where('sold_at', '>=', $monthStart)
            ->sum('total_amount');
        $revenueLastMonth = (float) Sale::whereBetween('sold_at', [$prevMonthStart, $prevMonthEnd])
            ->sum('total_amount');

        $pipeline = DB::table('sales')
            ->where('status', '!=', 'completed')
            ->where(function ($q) use ($monthStart) {
                $q->whereNull('sold_at')
                    ->orWhere('created_at', '>=', $monthStart);
            })
            ->sum('total_amount');

        $lowStockThreshold = 10;

        $lowStockLots = ProductLot::whereRaw('quantity_kg - COALESCE((SELECT SUM(quantity_kg) FROM sale_items WHERE sale_items.product_lot_id = product_lots.id), 0) < ?', [$lowStockThreshold])
            ->with('product:id,business_name')
            ->limit(5)
            ->get();

        $lowStockCount = (int) ProductLot::whereRaw('quantity_kg - COALESCE((SELECT SUM(quantity_kg) FROM sale_items WHERE sale_items.product_lot_id = product_lots.id), 0) < ?', [$lowStockThreshold])
            ->count();

        $overdueInvoices = CommunicationLog::where('channel', 'email')
            ->where('status', 'pending')
            ->where('created_at', '<', $now->subDays(7))
            ->count();

        $pendingPayments = Sale::where('status', 'pending')
            ->where('sold_at', '<', $now->subDays(3))
            ->sum('total_amount');

        $pendingDeliveryQuery = Sale::where('status', 'processing')
            ->where('sold_at', '<', $now->subDays(7));
        $pendingDeliveryCount = $pendingDeliveryQuery->count();
        $pendingDeliveries = $pendingDeliveryQuery->limit(3)->get();

        $newEmails = CommunicationLog::where('channel', 'email')
            ->where('direction', 'inbound')
            ->where('status', 'sent')
            ->where('created_at', '>=', $now->subDays(7))
            ->count();

        $incomingRFQs = CommunicationLog::where('channel', 'email')
            ->where('direction', 'inbound')
            ->where('created_at', '>=', $now->subDays(3))
            ->count();

        $trend = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : ($revenueThisMonth > 0 ? 100.0 : 0.0);

        $dailyRevenue = Sale::where('sold_at', '>=', $monthStart)
            ->selectRaw('DATE(sold_at) as day, SUM(total_amount) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $chartDays = [];
        $chartRevenue = [];
        for ($i = 0; $i < $now->day; $i++) {
            $date = $now->copy()->startOfMonth()->addDays($i)->format('Y-m-d');
            $chartDays[] = $now->copy()->startOfMonth()->addDays($i)->format('M d');
            $chartRevenue[] = (float) ($dailyRevenue[$date]->total ?? 0);
        }

        $channelBreakdown = CommunicationLog::selectRaw('channel, COUNT(*) as count')
            ->groupBy('channel')
            ->pluck('count', 'channel')
            ->toArray();

        return view('modules.command.index', [
            'events' => $events,
            'userId' => auth()->id(),
            'todaysSales' => $todaysSaleItems,
            'metrics' => [
                'revenue' => $revenueThisMonth,
                'pipeline' => (float) $pipeline,
                'trend' => $trend,
                'lowStockCount' => $lowStockCount,
                'lowStockValue' => $lowStockLots->sum(function ($lot) {
                    return $lot->quantity_kg * ($lot->supplier_rate_per_kg ?? 0);
                }),
                'lowStockLots' => $lowStockLots,
                'overdueInvoices' => (float) $overdueInvoices,
                'pendingPayments' => (float) $pendingPayments,
                'pendingDeliveryCount' => $pendingDeliveryCount,
                'pendingDeliveries' => $pendingDeliveries,
                'newEmails' => $newEmails,
                'incomingRFQs' => $incomingRFQs,
            ],
            'charts' => [
                'revenue_labels' => $chartDays,
                'revenue_data' => $chartRevenue,
                'channel_labels' => array_keys($channelBreakdown),
                'channel_data' => array_values($channelBreakdown),
            ],
        ]);
    }

    public function agenda()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $weekEnd = Carbon::today()->addDays(7);

        $events = Event::where('user_id', $user->id)
            ->where('start_time', '>=', $today->startOfDay())
            ->where('start_time', '<', $weekEnd->endOfDay())
            ->orderBy('start_time')
            ->get();

        $todayEvents = $events->filter(function ($event) use ($today) {
            return $event->start_time->isToday();
        })->values();

        $upcomingEvents = $events->filter(function ($event) use ($today) {
            return !$event->start_time->isToday();
        })->values();

        $customers = Customer::where('active', true)->get(['id', 'name', 'type']);

        return view('modules.command.agenda', [
            'user' => $user,
            'todayEvents' => $todayEvents,
            'upcomingEvents' => $upcomingEvents,
            'today' => $today,
            'customers' => $customers,
        ]);
    }

    public function editEvent(Event $event)
    {
        if (!auth()->user()->is($event->user) && !auth()->user()->hasRole('owner')) {
            abort(403);
        }
        return response()->json($event);
    }

    public function storeEvent(Request $request)
    {
        $user = auth()->user();
        $event = new Event();
        $event->user_id = $user->id;
        $event->type = $request->input('type', 'meeting');
        $event->title = $request->input('title');
        $event->title_ar = $request->input('title_ar');
        $event->start_time = Carbon::parse($request->input('start_time'));
        $event->end_time = $request->input('end_time') ? Carbon::parse($request->input('end_time')) : null;
        $event->location = $request->input('location');
        $event->location_ar = $request->input('location_ar');
        $event->host_name = $request->input('host_name');
        $event->host_name_ar = $request->input('host_name_ar');
        $event->guest_of_honor = $request->input('guest_of_honor');
        $event->guest_of_honor_ar = $request->input('guest_of_honor_ar');
        $event->customer_id = $request->input('customer_id');
        $event->protocol_notes = $request->input('protocol_notes');
        $event->protocol_notes_ar = $request->input('protocol_notes_ar');
        $event->dietary_restrictions = $request->input('dietary_restrictions');
        $event->dietary_restrictions_ar = $request->input('dietary_restrictions_ar');
        $event->required_materials = $request->input('required_materials_input') ? array_filter(explode(',', $request->input('required_materials_input'))) : [];
        $event->checklist_items = $request->input('checklist_items') ? json_decode($request->input('checklist_items'), true) : [];
        $event->priority = $request->input('priority', 'normal');
        $event->status = $request->input('status', 'scheduled');
        $event->tags = $request->input('tags_input') ? array_filter(explode(',', $request->input('tags_input'))) : [];
        $event->save();

        return redirect()->route('command.agenda')->with('success', __('Event created.'));
    }

    public function updateEvent(Request $request, Event $event)
    {
        if (!auth()->user()->is($event->user) && !auth()->user()->hasRole('owner')) {
            abort(403);
        }
        $event->title = $request->input('title');
        $event->title_ar = $request->input('title_ar');
        $event->start_time = Carbon::parse($request->input('start_time'));
        $event->end_time = $request->input('end_time') ? Carbon::parse($request->input('end_time')) : null;
        $event->location = $request->input('location');
        $event->location_ar = $request->input('location_ar');
        $event->host_name = $request->input('host_name');
        $event->host_name_ar = $request->input('host_name_ar');
        $event->guest_of_honor = $request->input('guest_of_honor');
        $event->guest_of_honor_ar = $request->input('guest_of_honor_ar');
        $event->customer_id = $request->input('customer_id');
        $event->protocol_notes = $request->input('protocol_notes');
        $event->protocol_notes_ar = $request->input('protocol_notes_ar');
        $event->dietary_restrictions = $request->input('dietary_restrictions');
        $event->dietary_restrictions_ar = $request->input('dietary_restrictions_ar');
        $event->required_materials = $request->input('required_materials_input') ? array_filter(explode(',', $request->input('required_materials_input'))) : [];
        $event->checklist_items = $request->input('checklist_items') ? json_decode($request->input('checklist_items'), true) : [];
        $event->priority = $request->input('priority', 'normal');
        $event->status = $request->input('status', 'scheduled');
        $event->tags = $request->input('tags_input') ? array_filter(explode(',', $request->input('tags_input'))) : [];
        $event->save();

        return redirect()->route('command.agenda')->with('success', __('Event updated.'));
    }

    public function destroyEvent(Event $event)
    {
        if (!auth()->user()->is($event->user) && !auth()->user()->hasRole('owner')) {
            abort(403);
        }
        $event->delete();
        return redirect()->route('command.agenda')->with('success', __('Event deleted.'));
    }

    public function delegations()
    {
        $user = auth()->user();
        $statuses = DelegationStatus::orderBy('sort_order')->get();

        $statusCodes = $statuses->pluck('code')->toArray();
        $placeholders = implode(',', array_fill(0, count($statusCodes), '?'));

        $delegations = Delegation::where('assignee_id', $user->id)
            ->orderByRaw("FIELD(status, {$placeholders})", $statusCodes)
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'normal', 'low')")
            ->orderBy('due_date')
            ->get();

        return view('modules.command.delegations', [
            'user' => $user,
            'delegations' => $delegations,
            'statuses' => $statuses,
        ]);
    }

    public function storeDelegation(Request $request)
    {
        $user = auth()->user();
        $delegation = new Delegation();
        $delegation->assignee_id = $user->id;
        $delegation->assigned_by_id = $user->id;
        $delegation->task_name = $request->input('title');
        $delegation->task_name_ar = $request->input('title_ar', $request->input('title'));
        $delegation->description = $request->input('description');
        $delegation->description_ar = $request->input('description_ar', $request->input('description'));
        $delegation->priority = $request->input('priority', 'normal');
        $delegation->status = 'not_started';
        $delegation->due_date = $request->input('due_date') ? Carbon::parse($request->input('due_date')) : null;
        $delegation->flag_reason = $request->input('flag_reason');
        $delegation->save();

        return redirect()->route('command.delegations')->with('success', __('Delegation created.'));
    }

    public function updateDelegation(Request $request, Delegation $delegation)
    {
        if ($delegation->assignee_id !== auth()->id() && !auth()->user()->hasRole('owner')) {
            abort(403);
        }
        $delegation->task_name = $request->input('title');
        $delegation->description = $request->input('description');
        $delegation->priority = $request->input('priority', 'normal');
        $delegation->due_date = $request->input('due_date') ? Carbon::parse($request->input('due_date')) : null;
        $delegation->flag_reason = $request->input('flag_reason');
        $delegation->save();

        return redirect()->route('command.delegations')->with('success', __('Delegation updated.'));
    }

    public function destroyDelegation(Delegation $delegation)
    {
        if ($delegation->assignee_id !== auth()->id() && !auth()->user()->hasRole('owner')) {
            abort(403);
        }
        $delegation->delete();
        return redirect()->route('command.delegations')->with('success', __('Delegation deleted.'));
    }

    public function updateDelegationStatus(Request $request, Delegation $delegation)
    {
        $delegation->status = $request->input('status');
        $delegation->save();

        return response()->json(['status' => 'ok']);
    }

    public function majlis()
    {
        $sessions = auth()->user()->majlisSessions ?? collect();
        return view('modules.command.majlis', [
            'sessions' => $sessions,
            'userId' => auth()->id(),
        ]);
    }
}
