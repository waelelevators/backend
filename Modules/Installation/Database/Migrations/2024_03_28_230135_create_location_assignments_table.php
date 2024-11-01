<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_assignments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stage_id')->constrained()->references('id')->on('stages');
            $table->foreignId('contract_id')->constrained()->references('id')->on('contracts');
            $table->integer('financial_status');
            $table->foreignId('representative_by')->constrained()->references('id')->on('employees')->nullable();
            $table->foreignId('user_id')->constrained();

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
        Schema::dropIfExists('location_assignments');
    }
}
