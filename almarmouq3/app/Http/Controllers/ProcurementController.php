<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    public function index()
    {
        return view('modules.procurement.index', [
            'userId' => auth()->id(),
            'stats' => [
                'pending_orders' => Sale::where('status', 'pending')->count(),
                'total_orders' => Sale::count(),
            ],
        ]);
    }

    public function orders()
    {
        $orders = Sale::orderBy('created_at', 'desc')->get();
        return view('modules.procurement.orders', [
            'orders' => $orders,
            'userId' => auth()->id(),
        ]);
    }

    public function inbound()
    {
        return view('modules.procurement.inbound', [
            'userId' => auth()->id(),
        ]);
    }

    public function forecast()
    {
        return view('modules.procurement.forecast', [
            'userId' => auth()->id(),
        ]);
    }
}
