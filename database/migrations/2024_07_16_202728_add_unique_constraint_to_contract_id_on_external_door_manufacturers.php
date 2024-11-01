<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToContractIdOnExternalDoorManufacturers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('external_door_manufacturers', function (Blueprint $table) {
            //
            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
            $table->unique('contract_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('external_door_manufacturers', function (Blueprint $table) {
            //
            $table->dropUnique(['contract_id']);
        });
    }
}
