<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_visits', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('maintenance_contract_id')->unsigned();
            $table->dateTime('visit_start_date');
            $table->dateTime('visit_end_date');
            $table->string('status');
            $table->string('images')->nullable();
            $table->string('technician_id');
            $table->string('user_id');
            $table->string('notes')->nullable();
            $table->string('test_checklist')->nullable();
            $table->boolean('customer_approval')->default(false);
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
        Schema::dropIfExists('maintenance_visits');
    }
}