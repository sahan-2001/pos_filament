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
        Schema::create('purchase_order_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('register_arrival_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->decimal('stored_quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total', 12, 2)->virtualAs('unit_price * stored_quantity');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_invoice_items');
    }
};
