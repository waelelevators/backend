<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractTechnicianAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_technician_assignments', function (Blueprint $table) {
            $table->id();
            $table->integer('contract_id');
            $table->integer('technician_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('status');
            $table->integer('stage_id');
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
        Schema::dropIfExists('contract_technician_assignments');
    }
}
