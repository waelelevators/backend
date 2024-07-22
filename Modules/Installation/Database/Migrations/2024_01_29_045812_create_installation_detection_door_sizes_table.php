<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallationDetectionDoorSizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installation_detection_door_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id');
            $table->foreignId('floor_id');
            $table->string('right_shoulder_size');
            $table->string('door_height');
            $table->string('door_size');
            $table->string('well_width');
            $table->string('well_depth');
            $table->string('floor_height');
            $table->string('left_shoulder_size');
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
        Schema::dropIfExists('installation_detection_door_sizes');
    }
}
