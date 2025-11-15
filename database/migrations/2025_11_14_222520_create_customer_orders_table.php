<?php
// database/migrations/2024_01_01_000001_create_customer_orders_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customer_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique();
            
            // Change this line to reference the custom primary key
            $table->foreignId('customer_id')->constrained('customers', 'customer_id')->onDelete('cascade');
            
            // Order details
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('remaining_amount', 12, 2)->default(0);
            
            // Status
            $table->enum('order_status', [
                'pending', 'confirmed', 'processing', 
                'shipped', 'delivered', 'cancelled', 'refunded'
            ])->default('pending');
            
            $table->enum('payment_status', [
                'pending', 'paid', 'failed', 'refunded', 'partially_refunded'
            ])->default('pending');
            
            $table->enum('payment_method', [
                'cash', 'credit_card', 'debit_card', 'bank_transfer', 'digital_wallet'
            ])->nullable();
            
            // Shipping information
            $table->text('shipping_address_line_1')->nullable();
            $table->text('shipping_address_line_2')->nullable();
            $table->string('shipping_city')->nullable();
            $table->string('shipping_state')->nullable();
            $table->string('shipping_zip_code')->nullable();
            $table->string('shipping_country')->nullable();
            
            // Billing information
            $table->text('billing_address_line_1')->nullable();
            $table->text('billing_address_line_2')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_zip_code')->nullable();
            $table->string('billing_country')->nullable();
            
            // Dates
            $table->date('ordered_date')->nullable();
            $table->date('wanted_delivery_date')->nullable();
            $table->date('planned_delivery_date')->nullable();
            $table->date('promissed_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();

            // Timestamps
            $table->timestamp('order_date')->useCurrent();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('order_id');
            $table->index('order_status');
            $table->index('payment_status');
            $table->index('order_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_orders');
    }
};