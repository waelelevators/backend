<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToContractIdInInternalDoorManufacturersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('internal_door_manufacturers', function (Blueprint $table) {
            //
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
        Schema::table('internal_door_manufacturers', function (Blueprint $table) {
            //
            $table->dropUnique(['contract_id']);
        });
    }
}
