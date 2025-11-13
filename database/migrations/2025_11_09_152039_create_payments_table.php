<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('draft_bill_id')->nullable()->constrained('draft_invoices')->onDelete('cascade');
            
            $table->enum('payment_method', ['cash', 'card', 'cheque', 'credit']);
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('amount_received', 10, 2)->nullable();
            $table->decimal('balance', 10, 2)->nullable();
            $table->string('reference_number')->nullable();
            $table->string('cheque_number')->nullable();
            $table->string('bank')->nullable();
            $table->text('remarks')->nullable();
            $table->dateTime('payment_date');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
            
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};