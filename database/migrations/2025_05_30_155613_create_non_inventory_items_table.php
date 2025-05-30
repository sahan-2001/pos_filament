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
        Schema::create('non_inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_id')->nullable();
            $table->string('name');
            $table->unsignedBigInteger('non_inventory_category_id');
            $table->decimal('price', 10, 2);
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('non_inventory_category_id')
                ->references('id')
                ->on('non_inventory_categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('non_inventory_items');
    }
};
