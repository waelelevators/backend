<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMalfunctionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // $table->foreignId('m_location_id')->constrained()
        // ->references('id')->on('maintenance_location_detections');

        Schema::create('malfunctions', function (Blueprint $table) {
            $table->id();
            $table->dateTime('started_date');
            $table->foreignId('m_id')->constrained()
                ->references('id')->on('maintenances');

            $table->foreignId('status_id')->constrained()
                ->references('id')->on('malfunction_statuses');
            $table->dateTime('ended_date')->nullable();

            $table->foreignId('redirect_to_id')->constrained()
                ->references('id')->on('users');
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
        Schema::dropIfExists('malfunctions');
    }
}
