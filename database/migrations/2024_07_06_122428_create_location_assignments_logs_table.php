<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationAssignmentsLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_assignments_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('location_assignment_id');
            $table->unsignedBigInteger('representative_by')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->timestamp('logged_at');
            $table->integer('status');
            $table->timestamps();

            $table->foreign('location_assignment_id')->references('id')->on('location_assignments')->onDelete('cascade');
            $table->foreign('representative_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_assignments_logs');
    }
}
