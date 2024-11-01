<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechniciansWorkOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technicians_work_orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('technician_id')->constrained()->references('id')->on('employees');
            $table->foreignId('work_order_id')->constrained()->references('id')->on('work_orders');
            $table->foreignId('stage_id')->constrained()->references('id')->on('stages');
            $table->foreignId('assignment_id')->constrained()->references('id')->on('location_statuses');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('technicians_work_orders');
    }
}
