<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Payment;
use App\Http\Requests\StorePawnRequest;
use App\Http\Requests\StoreRenewalRequest;
use App\Http\Requests\StoreRedemptionRequest;
use App\Services\TransactionService;
use App\Services\ReceiptService;
use App\Services\AuditService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $transactionService;
    protected $receiptService;

    public function __construct(TransactionService $transactionService, ReceiptService $receiptService)
    {
        $this->transactionService = $transactionService;
        $this->receiptService = $receiptService;
    }

    /**
     * Display a listing of all transactions
     */
    public function index(Request $request)
    {
        $query = Transaction::with('customer', 'item', 'payments');
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('customer', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            })->orWhere('pawn_ticket_number', 'like', "%{$search}%");
        }
        
        $transactions = $query->paginate(15);
        
        return view('transactions.index', compact('transactions'));
    }

    /**
     * Show pawn transaction form
     */
    public function createPawn()
    {
        $customers = Customer::all();
        return view('transactions.create-pawn', compact('customers'));
    }

    /**
     * Store a new pawn transaction
     */
    public function storePawn(StorePawnRequest $request)
    {
        try {
            $customer = Customer::findOrFail($request->customer_id);
            
            // Create or get item
            $item = Item::create([
                'customer_id' => $customer->id,
                'item_name' => $request->item_name,
                'item_description' => $request->item_description,
                'category' => $request->category,
                'assessed_value' => $request->assessed_value,
                'condition' => $request->condition,
            ]);
            
            AuditService::logCreate($item);
            
            // Create pawn transaction
            $transaction = $this->transactionService->createPawnTransaction($customer, $item, [
                'loan_amount' => $request->loan_amount,
                'interest_rate' => $request->interest_rate,
                'term_days' => $request->term_days,
            ]);
            
            AuditService::logCreate($transaction);
            
            // Generate receipt
            $receipt = $this->receiptService->generatePawnReceipt($transaction);
            
            return redirect()->route('transactions.show', $transaction->id)
                ->with('success', 'Pawn transaction created successfully!')
                ->with('receipt', $receipt);
        } catch (\Exception $e) {
            AuditService::logAction('error', 'Failed to create pawn transaction: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show renewal form
     */
    public function createRenewal($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        
        if ($transaction->status !== 'active') {
            return back()->with('error', 'Only active transactions can be renewed.');
        }
        
        return view('transactions.create-renewal', compact('transaction'));
    }

    /**
     * Process pawn renewal
     */
    public function storeRenewal(StoreRenewalRequest $request, $transactionId)
    {
        try {
            $transaction = Transaction::findOrFail($transactionId);
            $oldValues = $transaction->toArray();
            
            $transaction = $this->transactionService->renewPawnTransaction($transaction, [
                'term_days' => $request->term_days,
                'interest_rate' => $request->interest_rate ?? $transaction->interest_rate,
            ]);
            
            AuditService::logUpdate($transaction, $oldValues);
            
            // Create payment record
            $payment = Payment::create([
                'transaction_id' => $transaction->id,
                'amount_paid' => $this->transactionService->calculateInterest(
                    $transaction->loan_amount,
                    $request->interest_rate ?? $transaction->interest_rate,
                    $request->term_days
                ),
                'payment_date' => now()->toDateString(),
                'payment_type' => 'interest_payment',
                'receipt_number' => 'RCP' . date('YmdHis'),
            ]);
            
            AuditService::logCreate($payment);
            
            $receipt = $this->receiptService->generateRenewalReceipt($transaction, $payment);
            
            return redirect()->route('transactions.show', $transaction->id)
                ->with('success', 'Pawn renewal processed successfully!')
                ->with('receipt', $receipt);
        } catch (\Exception $e) {
            AuditService::logAction('error', 'Failed to process renewal: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show redemption form
     */
    public function createRedemption($transactionId)
    {
        $transaction = Transaction::findOrFail($transactionId);
        
        if ($transaction->status !== 'active') {
            return back()->with('error', 'Only active transactions can be redeemed.');
        }
        
        $amountDue = $this->transactionService->getAmountDue($transaction);
        
        return view('transactions.create-redemption', compact('transaction', 'amountDue'));
    }

    /**
     * Process redemption
     */
    public function storeRedemption(StoreRedemptionRequest $request, $transactionId)
    {
        try {
            $transaction = Transaction::findOrFail($transactionId);
            $oldValues = $transaction->toArray();
            
            $payment = Payment::create([
                'transaction_id' => $transaction->id,
                'amount_paid' => $request->amount_paid,
                'payment_date' => now()->toDateString(),
                'payment_type' => 'full_redemption',
                'receipt_number' => 'RCP' . date('YmdHis'),
                'notes' => $request->notes,
            ]);
            
            AuditService::logCreate($payment);
            
            // Process redemption
            $result = $this->transactionService->processRedemption($transaction, $payment);
            
            if ($result) {
                AuditService::logUpdate($transaction, $oldValues);
                
                $receipt = $this->receiptService->generateRedemptionReceipt($transaction, $payment);
                
                return redirect()->route('transactions.show', $transaction->id)
                    ->with('success', 'Item redeemed successfully! Item is now ready for release.')
                    ->with('receipt', $receipt);
            } else {
                $payment->delete();
                return back()->with('error', 'Payment amount is insufficient for full redemption.');
            }
        } catch (\Exception $e) {
            AuditService::logAction('error', 'Failed to process redemption: ' . $e->getMessage());
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Display a specific transaction
     */
    public function show(Transaction $transaction)
    {
        $transaction->load('customer', 'item', 'payments');
        $amountDue = $this->transactionService->getAmountDue($transaction);
        $auditLogs = AuditService::getLogsForModel('Transaction', $transaction->id);
        
        return view('transactions.show', compact('transaction', 'amountDue', 'auditLogs'));
    }

    /**
     * Display transaction edit form
     */
    public function edit(Transaction $transaction)
    {
        return view('transactions.edit', compact('transaction'));
    }

    /**
     * Update transaction
     */
    public function update(Request $request, Transaction $transaction)
    {
        $oldValues = $transaction->toArray();
        
        $transaction->update($request->validated());
        
        AuditService::logUpdate($transaction, $oldValues);
        
        return redirect()->route('transactions.show', $transaction->id)
            ->with('success', 'Transaction updated successfully!');
    }

    /**
     * Get transaction details via AJAX
     */
    public function getDetails($pawnTicketNumber)
    {
        $transaction = Transaction::where('pawn_ticket_number', $pawnTicketNumber)
            ->with('customer', 'item')
            ->firstOrFail();
        
        $amountDue = $this->transactionService->getAmountDue($transaction);
        
        return response()->json([
            'transaction' => $transaction,
            'customer' => $transaction->customer,
            'item' => $transaction->item,
            'amount_due' => $amountDue,
        ]);
    }
}

