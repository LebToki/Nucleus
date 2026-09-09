<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Http\Request;

class LogisticsController extends Controller
{
    public function index()
    {
        return view('modules.logistics.index', [
            'userId' => auth()->id(),
            'stats' => [
                'in_transit' => Sale::where('status', 'shipped')->count(),
                'delivered' => Sale::where('status', 'delivered')->count(),
                'showcases' => Customer::count(),
            ],
        ]);
    }

    public function whiteGlove()
    {
        return view('modules.logistics.white-glove', [
            'userId' => auth()->id(),
        ]);
    }

    public function courier()
    {
        return view('modules.logistics.courier', [
            'userId' => auth()->id(),
        ]);
    }

    public function showcases()
    {
        return view('modules.logistics.showcases', [
            'userId' => auth()->id(),
        ]);
    }
}
