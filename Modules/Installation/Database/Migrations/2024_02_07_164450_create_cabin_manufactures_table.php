<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCabinManufacturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cabin_manufactures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()
                ->references('id')->on('contracts');
            $table->string('weight_dbg');
            $table->foreignId('weight_location_id')->constrained()
                ->references('id')->on('weight_locations');
            $table->string('cabin_dbg');
            $table->foreignId('door_size_id')->constrained()
                ->references('id')->on('door_sizes');
            $table->string('machine_chair');

            $table->integer('door_direction_id');
            $table->foreignId('cover_type_id')->constrained()
                ->references('id')->on('cover_types');
            $table->string('machine_room_height');
            $table->string('machine_room_width');
            $table->string('machine_room_depth');
            $table->string('cabin_max_height');
            $table->string('last_floor_height');
            $table->dateTime('started_date');
            $table->string('order_attached');
            $table->foreignId('status_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    // 
    // $table->dateTime('accept_date');
    // $table->dateTime('ended_date');

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cabin_manufactures');
    }
}
