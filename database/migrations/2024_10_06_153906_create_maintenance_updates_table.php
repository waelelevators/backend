<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceUpdatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_contract_id')->constrained('maintenance_contracts');
            $table->string('status');
            $table->string('city_id');
            $table->string('user_id');
            $table->string('neighborhood_id');
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->string('customer_id');
            $table->string('elevator_type_id');
            $table->string('building_type_id');
            $table->integer('stops_count');
            $table->boolean('has_window')->default(false);
            $table->boolean('has_stairs')->default(false);
            $table->string('site_images')->nullable();
            // price
            $table->decimal('total', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('net_price', 10, 2);
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
        Schema::dropIfExists('maintenance_updates');
    }
}