<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sale;
use App\Models\CommunicationLog;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        return view('modules.administration.index', [
            'userId' => auth()->id(),
            'stats' => [
                'users' => User::count(),
                'sales' => Sale::count(),
                'communications' => CommunicationLog::count(),
            ],
        ]);
    }

    public function users()
    {
        $users = User::with('roles')->orderBy('created_at', 'desc')->get();
        return view('modules.administration.users', [
            'users' => $users,
            'userId' => auth()->id(),
        ]);
    }

    public function roles()
    {
        return view('modules.administration.roles', [
            'userId' => auth()->id(),
        ]);
    }

    public function commercialRules()
    {
        return view('modules.administration.commercial-rules', [
            'userId' => auth()->id(),
        ]);
    }

    public function audit()
    {
        $logs = CommunicationLog::orderBy('created_at', 'desc')->limit(100)->get();
        return view('modules.administration.audit', [
            'logs' => $logs,
            'userId' => auth()->id(),
        ]);
    }

    public function settings()
    {
        return view('modules.administration.settings', [
            'userId' => auth()->id(),
        ]);
    }
}
