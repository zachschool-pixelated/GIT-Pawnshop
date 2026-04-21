<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Show transaction report
     */
    public function transactions(Request $request)
    {
        $query = Transaction::with('customer', 'item', 'payments');
        
        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->paginate(50);
        
        // Calculate totals
        $totals = [
            'total_loans' => $query->sum('loan_amount'),
            'total_interest' => $query->sum('total_interest'),
            'total_paid' => $query->sum('amount_paid'),
            'count' => $query->count(),
        ];
        
        return view('reports.transactions', compact('transactions', 'totals'));
    }

    /**
     * Show customer report
     */
    public function customers(Request $request)
    {
        $query = Customer::withCount('transactions')
            ->withSum('transactions', 'loan_amount');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
        }
        
        $customers = $query->orderBy('transactions_count', 'desc')->paginate(50);
        
        // Calculate totals
        $totalCustomers = Customer::count();
        $totalTransactions = Transaction::count();
        $averageLoansPerCustomer = $totalTransactions > 0 ? $totalTransactions / $totalCustomers : 0;
        
        return view('reports.customers', compact('customers', 'totalCustomers', 'totalTransactions', 'averageLoansPerCustomer'));
    }

    /**
     * Show payment report
     */
    public function payments(Request $request)
    {
        $query = Payment::with('transaction.customer', 'transaction.item');
        
        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }
        
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        
        $payments = $query->orderBy('payment_date', 'desc')->paginate(50);
        
        // Calculate totals
        $totals = [
            'total_amount' => $query->sum('amount_paid'),
            'count' => $query->count(),
            'by_type' => Payment::selectRaw('payment_type, SUM(amount_paid) as total, COUNT(*) as count')
                ->groupBy('payment_type')
                ->get(),
        ];
        
        return view('reports.payments', compact('payments', 'totals'));
    }

    /**
     * Show audit log report
     */
    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user');
        
        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        $auditLogs = $query->orderBy('created_at', 'desc')->paginate(50);
        
        // Get action summary
        $actionSummary = AuditLog::selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->get();
        
        return view('reports.audit-logs', compact('auditLogs', 'actionSummary'));
    }

    /**
     * Show financial summary report
     */
    public function financialSummary(Request $request)
    {
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now()->endOfMonth();
        
        $summary = [
            'total_loans_issued' => Transaction::whereBetween('created_at', [$dateFrom, $dateTo])
                ->sum('loan_amount'),
            'total_interest_charged' => Transaction::whereBetween('created_at', [$dateFrom, $dateTo])
                ->sum('total_interest'),
            'total_interest_collected' => Payment::whereBetween('payment_date', [$dateFrom, $dateTo])
                ->where('payment_type', 'interest_payment')
                ->sum('amount_paid'),
            'total_redemptions' => Payment::whereBetween('payment_date', [$dateFrom, $dateTo])
                ->where('payment_type', 'full_redemption')
                ->sum('amount_paid'),
            'items_forfeited' => Transaction::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('status', 'forfeited')
                ->count(),
        ];
        
        // Calculate profitability metrics
        $summary['net_income'] = $summary['total_interest_collected'] - $summary['total_interest_charged'];
        $summary['redemption_rate'] = Transaction::whereBetween('created_at', [$dateFrom, $dateTo])->count() > 0 
            ? (Transaction::whereBetween('created_at', [$dateFrom, $dateTo])->where('status', 'redeemed')->count() 
                / Transaction::whereBetween('created_at', [$dateFrom, $dateTo])->count() * 100) 
            : 0;
        
        return view('reports.financial-summary', compact('summary', 'dateFrom', 'dateTo'));
    }

    /**
     * Export transaction report as CSV
     */
    public function exportTransactions(Request $request)
    {
        $query = Transaction::with('customer', 'item');
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $transactions = $query->get();
        
        $filename = 'transactions_' . now()->format('YmdHis') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Pawn Ticket', 'Customer', 'Item', 'Loan Amount', 'Interest Rate', 'Status', 'Created Date']);
            
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->pawn_ticket_number,
                    $transaction->customer->full_name,
                    $transaction->item->item_name,
                    $transaction->loan_amount,
                    $transaction->interest_rate,
                    $transaction->status,
                    $transaction->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
