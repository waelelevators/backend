<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceUpgradeElevatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_upgrade_elevators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_location_id')->constrained()
                ->references('id')->on('maintenance_location_detections');
            $table->json('elevators_parts');
            $table->float('total_cost');
            $table->float('discount')->default(0);;
            $table->float('tax')->default(0);;
            $table->integer('status')->default(0);
            $table->string('notes', 250)->nullable();
            $table->foreignId('done_by')->constrained()
                ->references('id')->on('users');
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
        Schema::dropIfExists('maintenance_upgrade_elevators');
    }
}
