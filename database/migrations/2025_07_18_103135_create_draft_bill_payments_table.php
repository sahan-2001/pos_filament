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
        Schema::create('draft_bill_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draft_invoice_id')->constrained()->onDelete('cascade');
            $table->decimal('order_total', 10, 2);
            $table->string('payment_type');
            $table->decimal('pay_amount', 10, 2);
            $table->decimal('cash_received', 10, 2)->nullable(); 
            $table->decimal('cash_balance', 10, 2)->nullable(); 
            $table->string('reference')->nullable(); 
            $table->string('bank')->nullable();
            $table->string('cheque_no')->nullable();
            $table->text('remarks')->nullable();

            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('draft_bill_payments');
    }
};