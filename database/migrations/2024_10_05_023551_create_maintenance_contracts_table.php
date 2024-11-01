<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_contracts', function (Blueprint $table) {

            $table->id();
            $table->string('contract_number');
            $table->string('area_id');
            $table->string('user_id');
            $table->string('contract_type')->default('draft');
            $table->decimal('total', 10, 2)->default(0);
            $table->string('city_id')->nullable();
            $table->string('neighborhood_id')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('client_id')->nullable();
            $table->string('elevator_type_id')->nullable();
            $table->string('building_type_id')->nullable();
            $table->integer('stops_count')->nullable();
            $table->boolean('has_window')->default(false);
            $table->boolean('has_stairs')->default(false);
            $table->json('site_images')->nullable();
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
        Schema::dropIfExists('maintenance_contracts');
    }
}
