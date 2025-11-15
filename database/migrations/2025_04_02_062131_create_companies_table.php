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
        // database/migrations/xxxx_xx_xx_create_companies_table.php
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('address_line_3')->nullable();
            $table->string('city');
            $table->string('postal_code');
            $table->string('country')->default('Sri Lanka');
            $table->string('primary_phone');
            $table->string('secondary_phone')->nullable();
            $table->string('email');
            $table->date('started_date');
            $table->string('logo')->nullable();
            $table->text('special_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->after('special_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
