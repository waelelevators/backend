<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToContracts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // $table->unsignedBigInteger('elevator_type_id')->nullable()->default(1);
        // $table->unsignedBigInteger('cabin_rails_size_id')->nullable()->default(1);
        // $table->unsignedBigInteger('counterweight_rails_size_id')->nullable()->default(1);
        // $table->unsignedBigInteger('stop_number_id')->nullable()->default(1);
        // $table->unsignedBigInteger('elevator_trip_id')->nullable()->default(1);
        // $table->unsignedBigInteger('elevator_room_id')->nullable()->default(1);
        // $table->unsignedBigInteger('machine_type_id')->nullable()->default(1);
        // $table->unsignedBigInteger('machine_warranty_id')->nullable()->default(1);
        // $table->unsignedBigInteger('machine_load_id')->nullable()->default(1);
        // $table->unsignedBigInteger('machine_speed_id')->nullable()->default(1);
        // $table->unsignedBigInteger('people_load_id')->nullable()->default(1);
        // $table->unsignedBigInteger('control_card_id')->nullable()->default(1);
        // $table->unsignedBigInteger('entrances_number_id')->nullable()->default(1);
        // $table->unsignedBigInteger('outer_door_direction_id')->nullable()->default(1);
        // $table->unsignedBigInteger('inner_door_type_id')->nullable()->default(1);
        // $table->unsignedBigInteger('door_size_id')->nullable()->default(1);


        Schema::table('contracts', function (Blueprint $table) {
            //
            $table->foreign('elevator_type_id')->references('id')->on('elevator_types')->onDelete('cascade');
            $table->foreign('cabin_rails_size_id')->references('id')->on('cabin_rails_sizes')->onDelete('cascade');
            $table->foreign('counterweight_rails_size_id')->references('id')->on('counterweight_rails_sizes')->onDelete('cascade');
            $table->foreign('elevator_trip_id')->references('id')->on('elevator_trips')->onDelete('cascade');
            $table->foreign('elevator_room_id')->references('id')->on('elevator_rooms')->onDelete('cascade');
            $table->foreign('machine_type_id')->references('id')->on('machine_types')->onDelete('cascade');
            //  $table->foreign('machine_warranty_id')->references('id')->on('elevator_warranties')->onDelete('cascade');
            // $table->foreign('machine_load_id')->references('id')->on('machine_loads')->onDelete('cascade');
            // $table->foreign('machine_speed_id')->references('id')->on('machine_speeds')->onDelete('cascade');
            // $table->foreign('people_load_id')->references('id')->on('people_loads')->onDelete('cascade');
            // $table->foreign('control_card_id')->references('id')->on('control_cards')->onDelete('cascade');
            // $table->foreign('outer_door_direction_id')->references('id')->on('outer_door_directions')->onDelete('cascade');
            // $table->foreign('inner_door_type_id')->references('id')->on('inner_door_types')->onDelete('cascade');
            // $table->foreign('door_size_id')->references('id')->on('door_sizes')->onDelete('cascade');
            // $table->foreign('stop_number_id')->references('id')->on('stops_numbers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            //
        });
    }
}
