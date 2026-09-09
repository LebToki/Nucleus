<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\RawInventoryItem;
use App\Models\Product;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function index()
    {
        return view('modules.financial.index', [
            'userId' => auth()->id(),
            'stats' => [
                'revenue_this_month' => Sale::whereMonth('created_at', now()->month)->sum('total'),
                'revenue_last_month' => Sale::whereMonth('created_at', now()->subMonth()->month)->sum('total'),
                'cogs' => RawInventoryItem::sum('total_cost'),
                'expenses' => 0,
            ],
        ]);
    }

    public function pnl()
    {
        $sales = Sale::orderBy('created_at', 'desc')->get();
        return view('modules.financial.pnl', [
            'sales' => $sales,
            'userId' => auth()->id(),
        ]);
    }

    public function expenses()
    {
        return view('modules.financial.expenses', [
            'userId' => auth()->id(),
        ]);
    }

    public function tax()
    {
        return view('modules.financial.tax', [
            'userId' => auth()->id(),
        ]);
    }
}
