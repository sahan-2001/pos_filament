<?php
// database/migrations/2024_01_01_000002_create_customer_order_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customer_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('restrict');
            
            // Product details at time of order
            $table->string('item_id');
            $table->string('item_code');
            $table->string('item_name');
            $table->string('category');
            $table->string('uom');
            $table->text('special_note')->nullable();
            
            // Pricing
            $table->decimal('unit_price', 12, 2);
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->integer('quantity');
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2);
            
            // Inventory tracking
            $table->boolean('inventory_deducted')->default(false);
            $table->timestamp('inventory_deducted_at')->nullable();
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('customer_order_id');
            $table->index('inventory_item_id');
            $table->index('item_code');
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_order_items');
    }
};