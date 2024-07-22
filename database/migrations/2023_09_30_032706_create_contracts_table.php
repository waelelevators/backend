<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('status');
            $table->decimal('total');
            $table->decimal('tax');
            $table->integer('client_id')->default(1);
            $table->string('project_name');
            $table->unsignedBigInteger('elevator_type_id')->nullable()->default(1);
            $table->unsignedBigInteger('cabin_rails_size_id')->nullable()->default(1);
            $table->unsignedBigInteger('counterweight_rails_size_id')->nullable()->default(1);
            $table->unsignedBigInteger('stop_number_id')->nullable()->default(1);
            $table->unsignedBigInteger('elevator_trip_id')->nullable()->default(1);
            $table->unsignedBigInteger('elevator_room_id')->nullable()->default(1);
            $table->unsignedBigInteger('machine_type_id')->nullable()->default(1);
            $table->unsignedBigInteger('machine_warranty_id')->nullable()->default(1);
            $table->unsignedBigInteger('machine_load_id')->nullable()->default(1);
            $table->unsignedBigInteger('machine_speed_id')->nullable()->default(1);
            $table->unsignedBigInteger('people_load_id')->nullable()->default(1);
            $table->unsignedBigInteger('control_card_id')->nullable()->default(1);
            $table->unsignedBigInteger('entrances_number_id')->nullable()->default(1);
            $table->unsignedBigInteger('outer_door_direction_id')->nullable()->default(1);
            $table->unsignedBigInteger('inner_door_type_id')->nullable()->default(1);
            $table->unsignedBigInteger('door_size_id')->nullable()->default(1);
            $table->string('other_additions', 250)->nullable();
            $table->unsignedBigInteger('visits_number')->nullable()->default(1);
            $table->unsignedBigInteger('free_maintenance_id')->nullable()->default(1);
            $table->unsignedBigInteger('elevator_warranty_id')->nullable()->default(1);
            $table->string('attachment')->nullable();
            $table->enum('contract_status', ['Draft', 'Completed', 'Other', 'assigned']);
            $table->integer('user_id');
            $table->integer('branch_id')->default(1);
            $table->string('contract_number', 200)->nullable();
            $table->integer('stage_id')->default(1);
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
        Schema::dropIfExists('contracts');
    }
};
