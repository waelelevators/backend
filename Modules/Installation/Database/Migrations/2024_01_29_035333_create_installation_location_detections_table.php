<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallationLocationDetectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installation_location_detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained();
         
            $table->json('location_data');
            $table->string('notes', 250)->nullable();
            $table->json('well_data');
            $table->string('shoulder_type');
            $table->json('machine_data');
            $table->json('floor_data');
            $table->integer('status')->default(1);
            $table->foreignId('representative_id')->constrained()->references('id')->on('representatives');
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
        Schema::dropIfExists('installation_location_detections');
    }
}
