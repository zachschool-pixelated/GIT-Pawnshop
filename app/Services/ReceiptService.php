<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Payment;

class ReceiptService
{
    /**
     * Generate pawn transaction receipt
     */
    public function generatePawnReceipt(Transaction $transaction): array
    {
        return [
            'receipt_type' => 'PAWN_RECEIPT',
            'pawn_ticket' => $transaction->pawn_ticket_number,
            'date' => $transaction->created_at->format('m/d/Y H:i:s'),
            'customer' => $transaction->customer->full_name,
            'customer_phone' => $transaction->customer->phone,
            'item' => $transaction->item->item_name,
            'item_category' => $transaction->item->category,
            'item_condition' => $transaction->item->condition,
            'assessed_value' => number_format($transaction->item->assessed_value, 2),
            'loan_amount' => number_format($transaction->loan_amount, 2),
            'interest_rate' => $transaction->interest_rate . '%',
            'term_days' => $transaction->term_days . ' days',
            'maturity_date' => $transaction->maturity_date->format('m/d/Y'),
            'total_interest' => number_format($transaction->total_interest, 2),
            'total_amount_due' => number_format($transaction->loan_amount + $transaction->total_interest, 2),
        ];
    }

    /**
     * Generate renewal receipt
     */
    public function generateRenewalReceipt(Transaction $transaction, Payment $payment): array
    {
        return [
            'receipt_type' => 'RENEWAL_RECEIPT',
            'receipt_number' => $payment->receipt_number,
            'pawn_ticket' => $transaction->pawn_ticket_number,
            'date' => $payment->payment_date->format('m/d/Y'),
            'customer' => $transaction->customer->full_name,
            'customer_phone' => $transaction->customer->phone,
            'item' => $transaction->item->item_name,
            'amount_paid' => number_format($payment->amount_paid, 2),
            'payment_type' => 'Renewal Interest Payment',
            'new_maturity_date' => $transaction->maturity_date->format('m/d/Y'),
            'remaining_balance' => number_format($transaction->loan_amount, 2),
        ];
    }

    /**
     * Generate redemption receipt
     */
    public function generateRedemptionReceipt(Transaction $transaction, Payment $payment): array
    {
        return [
            'receipt_type' => 'REDEMPTION_RECEIPT',
            'receipt_number' => $payment->receipt_number,
            'pawn_ticket' => $transaction->pawn_ticket_number,
            'date' => $payment->payment_date->format('m/d/Y'),
            'customer' => $transaction->customer->full_name,
            'customer_phone' => $transaction->customer->phone,
            'item' => $transaction->item->item_name,
            'item_category' => $transaction->item->category,
            'original_loan_amount' => number_format($transaction->loan_amount, 2),
            'total_interest' => number_format($transaction->total_interest, 2),
            'total_amount_due' => number_format($transaction->loan_amount + $transaction->total_interest, 2),
            'amount_paid' => number_format($payment->amount_paid, 2),
            'status' => 'REDEEMED - Item Released',
        ];
    }

    /**
     * Generate payment receipt
     */
    public function generatePaymentReceipt(Transaction $transaction, Payment $payment): array
    {
        $receiptData = [
            'receipt_number' => $payment->receipt_number,
            'pawn_ticket' => $transaction->pawn_ticket_number,
            'date' => $payment->payment_date->format('m/d/Y H:i:s'),
            'customer' => $transaction->customer->full_name,
            'customer_phone' => $transaction->customer->phone,
            'amount_paid' => number_format($payment->amount_paid, 2),
            'payment_type' => $payment->payment_type,
            'payment_notes' => $payment->notes ?? 'N/A',
        ];

        if ($payment->payment_type === 'full_redemption') {
            $receiptData['receipt_type'] = 'REDEMPTION_RECEIPT';
            $receiptData['item'] = $transaction->item->item_name;
            $receiptData['status'] = 'ITEM RELEASED';
        } else {
            $receiptData['receipt_type'] = 'PAYMENT_RECEIPT';
            $receiptData['new_maturity_date'] = $transaction->maturity_date->format('m/d/Y');
        }

        return $receiptData;
    }

    /**
     * Format receipt for printing
     */
    public function formatReceiptForPrinting(array $receipt): string
    {
        $output = "\n";
        $output .= "========================================\n";
        $output .= "     CPB-NGI PAWNSHOP INC.\n";
        $output .= "     Davao City, Philippines\n";
        $output .= "========================================\n\n";
        
        $output .= "Receipt Type: " . $receipt['receipt_type'] . "\n";
        
        foreach ($receipt as $key => $value) {
            if ($key !== 'receipt_type') {
                $formattedKey = str_replace('_', ' ', ucwords($key, '_'));
                $output .= "$formattedKey: $value\n";
            }
        }
        
        $output .= "\n========================================\n";
        $output .= "Thank you for your transaction!\n";
        $output .= "========================================\n\n";

        return $output;
    }
}
