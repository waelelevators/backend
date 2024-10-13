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
            $table->string('contract_number')->unique();
            $table->string('area');
            $table->string('user_id');
            $table->enum('contract_type', ['contract', 'draft'])->default('draft');
            $table->decimal('total', 10, 2);
            $table->string('city_id');
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