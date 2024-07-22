<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDoorsNumberToExternalDoorManufacturers extends Migration
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
            $table->integer('doors_number');
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
            $table->dropColumn('doors_number');
        });
    }
}
