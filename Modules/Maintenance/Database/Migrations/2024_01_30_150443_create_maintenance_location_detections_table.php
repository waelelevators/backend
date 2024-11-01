<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceLocationDetectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_location_detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained();
            $table->string('main_quotation_number')->nullable();
            $table->string('projects_name')->nullable();
            $table->json('location_data');
            $table->json('elevator_data');
            $table->string('notes', 250)->nullable();
            $table->integer('status')->default(0);
            $table->integer('how_did_you_get_to_us');
            $table->foreignId('detection_by')->constrained()->references('id')->on('users');
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
        Schema::dropIfExists('maintenance_location_detections');
    }
}
