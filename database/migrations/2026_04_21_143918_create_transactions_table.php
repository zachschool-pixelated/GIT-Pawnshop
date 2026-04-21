<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->enum('transaction_type', ['pawn', 'renewal', 'redemption', 'auction']);
            $table->decimal('loan_amount', 12, 2);
            $table->decimal('interest_rate', 5, 2); // percentage
            $table->integer('term_days'); // loan period in days
            $table->date('maturity_date');
            $table->enum('status', ['active', 'redeemed', 'forfeited', 'auctioned'])->default('active');
            $table->decimal('total_interest', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->string('pawn_ticket_number')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
