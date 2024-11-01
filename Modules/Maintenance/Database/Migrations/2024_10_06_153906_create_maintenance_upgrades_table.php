clccccccc<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceUpgradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_upgrades', function (Blueprint $table) {
            $table->id();
            $table->string('status');
            $table->foreignId('city_id');
            $table->foreignId('user_id');
            $table->foreignId('maintenance_contract_id');
            $table->foreignId('neighborhood_id');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->foreignId('client_id');
            $table->foreignId('elevator_type_id');
            $table->foreignId('building_type_id');
            $table->integer('stops_count');
            $table->boolean('has_window')->default(false);
            $table->boolean('has_stairs')->default(false);
            $table->json('site_images')->nullable();
            $table->decimal('total', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('net_price', 10, 2);
            $table->string('attachment_contract')->nullable();
            $table->string('attachment_receipt')->nullable();
            $table->foreignId('speed_id');
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
        Schema::dropIfExists('maintenance_upgrades');
    }
}
