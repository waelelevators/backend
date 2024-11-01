<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenanceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_info_id')->constrained()
                ->references('id')->on('maintenance_infos');
            $table->foreignId('m_id')->constrained()
                ->references('id')->on('maintenances');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('area_id')->constrained()
                ->references('id')->on('areas');
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
        Schema::dropIfExists('maintenance_logs');
    }
}
