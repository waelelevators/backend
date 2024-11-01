<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            //

            //  $table->bigInteger('stage_id')->change();
            //  $table->bigInteger('user_id')->change();
            // $table->unsignedBigInteger('branch_id')->nullable();
            // $table->unsignedBigInteger('elevator_warranty_id')->nullable();
            // $table->unsignedBigInteger('free_maintenance_id')->nullable();
            // $table->unsignedBigInteger('door_size_id')->nullable();
            // $table->unsignedBigInteger('inner_door_type_id')->nullable();
            // $table->unsignedBigInteger('outer_door_direction_id')->nullable();
            // $table->unsignedBigInteger('entrances_number_id')->nullable();
            // $table->unsignedBigInteger('control_card_id')->nullable();
            // $table->unsignedBigInteger('people_load_id')->nullable();
            // $table->unsignedBigInteger('machine_speed_id')->nullable();
            // $table->unsignedBigInteger('machine_load_id')->nullable();
            // $table->unsignedBigInteger('machine_warranty_id')->nullable();
            // $table->unsignedBigInteger('stop_number_id')->nullable();


            // $table->foreign('stage_id')->references('id')->on('stages')->onDelete('set null');
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            // $table->foreign('elevator_warranty_id')->references('id')->on('elevator_warranties')->onDelete('set null');
            // $table->foreign('free_maintenance_id')->references('id')->on('elevator_warranties')->onDelete('set null');
            // $table->foreign('door_size_id')->references('id')->on('door_sizes')->onDelete('set null');
            // $table->foreign('inner_door_type_id')->references('id')->on('inner_door_types')->onDelete('set null');
            // $table->foreign('outer_door_direction_id')->references('id')->on('outer_door_directions')->onDelete('set null');
            // $table->foreign('entrances_number_id')->references('id')->on('entrances_numbers')->onDelete('set null');
            // $table->foreign('control_card_id')->references('id')->on('control_cards')->onDelete('set null');
            // $table->foreign('people_load_id')->references('id')->on('people_loads')->onDelete('set null');
            // $table->foreign('machine_speed_id')->references('id')->on('machine_speeds')->onDelete('set null');
            // $table->foreign('machine_load_id')->references('id')->on('machine_loads')->onDelete('set null');
            // $table->foreign('machine_warranty_id')->references('id')->on('elevator_warranties')->onDelete('set null');
            $table->foreign('stop_number_id')->references('id')->on('stops_numbers')->onDelete('set null');
        });
    }

    // $table->foreign('stop_number_id')->references('id')->on('stop_numbers')->onDelete('set null');
    // $table->foreign('elevator_trip_id')->references('id')->on('elevator_trips')->onDelete('set null');
    // $table->foreign('elevator_type_id')->references('id')->on('elevator_types')->onDelete('set null');
    // $table->foreign('elevator_room_id')->references('id')->on('elevator_rooms')->onDelete('set null');
    // $table->foreign('elevator_weight_id')->references('id')->on('elevator_weights')->onDelete('set null');
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
