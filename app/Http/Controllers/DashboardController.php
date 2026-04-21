<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $totalCustomers = Customer::count();
        $totalTransactions = Transaction::count();
        $activeTransactions = Transaction::where('status', 'active')->count();
        $redeemedTransactions = Transaction::where('status', 'redeemed')->count();
        $forfeitedItems = Transaction::where('status', 'forfeited')->count();
        
        // Get financial data
        $totalLoanedAmount = Transaction::sum('loan_amount');
        $totalInterestCollected = Payment::where('payment_type', 'interest_payment')->sum('amount_paid');
        $totalRedemptions = Payment::where('payment_type', 'full_redemption')->sum('amount_paid');
        
        // Get recent transactions
        $recentTransactions = Transaction::with('customer', 'item')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Get recent payments
        $recentPayments = Payment::with('transaction.customer')
            ->orderBy('payment_date', 'desc')
            ->take(10)
            ->get();
        
        // Get daily statistics for the last 7 days
        $dailyStats = $this->getDailyStatistics();
        
        // Get top customers by transaction count
        $topCustomers = Customer::withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->take(5)
            ->get();
        
        // Get transaction status breakdown
        $transactionStatusBreakdown = Transaction::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
        
        // Get items by category
        $itemsByCategory = DB::table('items')
            ->selectRaw('category, count(*) as count, AVG(assessed_value) as avg_value')
            ->groupBy('category')
            ->get();
        
        return view('dashboard', compact(
            'totalCustomers',
            'totalTransactions',
            'activeTransactions',
            'redeemedTransactions',
            'forfeitedItems',
            'totalLoanedAmount',
            'totalInterestCollected',
            'totalRedemptions',
            'recentTransactions',
            'recentPayments',
            'dailyStats',
            'topCustomers',
            'transactionStatusBreakdown',
            'itemsByCategory'
        ));
    }

    /**
     * Get daily statistics for the last 7 days
     */
    private function getDailyStatistics(): array
    {
        $stats = [];
        $today = Carbon::today();
        
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->clone()->subDays($i);
            $dateString = $date->format('Y-m-d');
            
            $stats[$dateString] = [
                'date' => $date->format('M d'),
                'transactions' => Transaction::whereDate('created_at', $dateString)->count(),
                'payments' => Payment::whereDate('payment_date', $dateString)->count(),
                'revenue' => Payment::whereDate('payment_date', $dateString)->sum('amount_paid'),
            ];
        }
        
        return $stats;
    }
}
