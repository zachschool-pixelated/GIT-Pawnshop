<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Item;
use Carbon\Carbon;

class TransactionService
{
    /**
     * Create a new pawn transaction
     */
    public function createPawnTransaction(Customer $customer, Item $item, array $data): Transaction
    {
        $loanAmount = $data['loan_amount'];
        $interestRate = $data['interest_rate'];
        $termDays = $data['term_days'];
        
        // Calculate maturity date
        $maturityDate = Carbon::now()->addDays($termDays);
        
        // Calculate total interest
        $totalInterest = $this->calculateInterest($loanAmount, $interestRate, $termDays);
        
        // Generate pawn ticket number
        $pawnTicketNumber = $this->generatePawnTicketNumber();
        
        return Transaction::create([
            'customer_id' => $customer->id,
            'item_id' => $item->id,
            'transaction_type' => 'pawn',
            'loan_amount' => $loanAmount,
            'interest_rate' => $interestRate,
            'term_days' => $termDays,
            'maturity_date' => $maturityDate,
            'total_interest' => $totalInterest,
            'status' => 'active',
            'pawn_ticket_number' => $pawnTicketNumber,
        ]);
    }

    /**
     * Process pawn renewal
     */
    public function renewPawnTransaction(Transaction $transaction, array $data): Transaction
    {
        $currentDueDate = $transaction->maturity_date;
        $termDays = $data['term_days'] ?? $transaction->term_days;
        $interestRate = $data['interest_rate'] ?? $transaction->interest_rate;
        
        // Calculate new maturity date
        $newMaturityDate = Carbon::parse($currentDueDate)->addDays($termDays);
        
        // Calculate additional interest
        $additionalInterest = $this->calculateInterest($transaction->loan_amount, $interestRate, $termDays);
        
        // Update transaction
        $transaction->update([
            'term_days' => $termDays,
            'maturity_date' => $newMaturityDate,
            'total_interest' => $transaction->total_interest + $additionalInterest,
            'interest_rate' => $interestRate,
            'transaction_type' => 'renewal',
        ]);
        
        return $transaction;
    }

    /**
     * Process redemption (full payment)
     */
    public function processRedemption(Transaction $transaction, Payment $payment): bool
    {
        $amountDue = $transaction->loan_amount + $transaction->total_interest - $transaction->amount_paid;
        
        if ($payment->amount_paid >= $amountDue) {
            $transaction->update([
                'amount_paid' => $transaction->amount_paid + $amountDue,
                'status' => 'redeemed',
            ]);
            
            $payment->update(['payment_type' => 'full_redemption']);
            
            return true;
        }
        
        return false;
    }

    /**
     * Process interest payment (renewal)
     */
    public function processInterestPayment(Transaction $transaction, Payment $payment): bool
    {
        $amountDue = ($transaction->total_interest - $transaction->amount_paid);
        
        if ($payment->amount_paid <= $amountDue) {
            $transaction->update([
                'amount_paid' => $transaction->amount_paid + $payment->amount_paid,
            ]);
            
            $payment->update(['payment_type' => 'interest_payment']);
            
            return true;
        }
        
        return false;
    }

    /**
     * Calculate interest based on loan amount, rate, and term
     */
    public function calculateInterest(float $principal, float $rate, int $termDays): float
    {
        // Simple interest formula: Interest = Principal × Rate × Time
        // Rate is in percentage, term is in days (convert to months/years)
        $termMonths = $termDays / 30;
        return ($principal * ($rate / 100) * $termMonths);
    }

    /**
     * Generate unique pawn ticket number
     */
    public function generatePawnTicketNumber(): string
    {
        $prefix = 'PT' . date('Ymd');
        $count = Transaction::whereDate('created_at', today())->count() + 1;
        return $prefix . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get amount due for a transaction
     */
    public function getAmountDue(Transaction $transaction): float
    {
        return $transaction->loan_amount + $transaction->total_interest - $transaction->amount_paid;
    }

    /**
     * Mark transaction as forfeited (if not redeemed within maturity date)
     */
    public function markAsForfeited(Transaction $transaction): Transaction
    {
        if ($transaction->maturity_date < today() && $transaction->status === 'active') {
            $transaction->update(['status' => 'forfeited']);
        }
        
        return $transaction;
    }
}
