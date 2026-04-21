<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Transaction;
use App\Http\Requests\StorePaymentRequest;
use App\Services\ReceiptService;
use App\Services\AuditService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    /**
     * Display a listing of all payments
     */
    public function index(Request $request)
    {
        $query = Payment::with('transaction.customer', 'transaction.item');
        
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }
        
        $payments = $query->orderBy('payment_date', 'desc')->paginate(20);
        
        return view('payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment
     */
    public function create()
    {
        return view('payments.create');
    }

    /**
     * Store a newly created payment in storage
     */
    public function store(StorePaymentRequest $request)
    {
        try {
            $payment = Payment::create([
                'transaction_id' => $request->transaction_id,
                'amount_paid' => $request->amount_paid,
                'payment_date' => $request->payment_date,
                'payment_type' => $request->payment_type,
                'receipt_number' => 'RCP' . date('YmdHis'),
                'notes' => $request->notes,
            ]);
            
            AuditService::logCreate($payment);
            
            $transaction = $payment->transaction;
            $receipt = $this->receiptService->generatePaymentReceipt($transaction, $payment);
            
            return redirect()->route('payments.show', $payment->id)
                ->with('success', 'Payment recorded successfully!')
                ->with('receipt', $receipt);
        } catch (\Exception $e) {
            AuditService::logAction('error', 'Failed to create payment: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Display a specific payment
     */
    public function show(Payment $payment)
    {
        $payment->load('transaction.customer', 'transaction.item');
        $auditLogs = AuditService::getLogsForModel('Payment', $payment->id);
        
        return view('payments.show', compact('payment', 'auditLogs'));
    }

    /**
     * Show the form for editing a payment
     */
    public function edit(Payment $payment)
    {
        return view('payments.edit', compact('payment'));
    }

    /**
     * Update a payment in storage
     */
    public function update(Request $request, Payment $payment)
    {
        try {
            $oldValues = $payment->toArray();
            
            $payment->update($request->validate([
                'notes' => 'nullable|string|max:500',
            ]));
            
            AuditService::logUpdate($payment, $oldValues);
            
            return redirect()->route('payments.show', $payment->id)
                ->with('success', 'Payment updated successfully!');
        } catch (\Exception $e) {
            AuditService::logAction('error', 'Failed to update payment: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Remove a payment from storage (typically for void/reverse operations)
     */
    public function destroy(Payment $payment)
    {
        try {
            AuditService::logDelete($payment);
            
            // Log reversal as an action
            AuditService::logAction(
                'reversed',
                'Payment #' . $payment->id . ' reversed - Receipt: ' . $payment->receipt_number,
                ['payment_id' => $payment->id, 'amount' => $payment->amount_paid]
            );
            
            $payment->delete();
            
            return redirect()->route('payments.index')
                ->with('success', 'Payment reversed successfully!');
        } catch (\Exception $e) {
            AuditService::logAction('error', 'Failed to reverse payment: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Get payment details via AJAX
     */
    public function getDetails($receiptNumber)
    {
        $payment = Payment::where('receipt_number', $receiptNumber)
            ->with('transaction.customer', 'transaction.item')
            ->firstOrFail();
        
        return response()->json([
            'payment' => $payment,
            'transaction' => $payment->transaction,
        ]);
    }
}

