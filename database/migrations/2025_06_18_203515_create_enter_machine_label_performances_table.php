<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateEnterMachineLabelPerformancesTable extends Migration
{
    public function up()
    {
        Schema::create('enter_machine_label_performances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enter_performance_record_id');
            $table->unsignedBigInteger('cutting_label_id');
            $table->unsignedBigInteger('machine_id');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('enter_machine_label_performances');
    }
}