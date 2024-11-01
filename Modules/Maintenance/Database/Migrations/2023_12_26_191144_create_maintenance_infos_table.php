<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenanceInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_infos', function (Blueprint $table) {

            $table->id();
            $table->string('contract_number');
            $table->foreignId('client_id')->constrained();
            $table->foreignId('contract_id')->constrained()
                ->references('id')->on('contracts')->default(1);
            $table->string('project_name')->nullable();
            $table->json('location_data');
            $table->json('elevator_data');
            $table->integer('how_did_you_get_to_us');
            $table->integer('status')->default(0);
            $table->text('note')->nullable();
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
        Schema::dropIfExists('maintenance_infos');
    }
}
