<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $adminsCount = User::where('role', 'admin')->count();
        $tellersCount = User::where('role', 'teller')->count();

        $usersByRole = User::selectRaw('role, COUNT(*) as count')
            ->groupBy('role')
            ->orderBy('count', 'desc')
            ->get();

        $recentUsers = User::orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $systemSummary = [
            'customers' => Customer::count(),
            'transactions' => Transaction::count(),
            'active_transactions' => Transaction::where('status', 'active')->count(),
            'total_revenue' => Payment::sum('amount_paid'),
        ];

        return view('admin.dashboard', compact(
            'totalUsers',
            'adminsCount',
            'tellersCount',
            'usersByRole',
            'recentUsers',
            'systemSummary'
        ));
    }
}
