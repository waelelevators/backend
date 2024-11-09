<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyColumnToNullableInInstallationLocationDetectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('installation_location_detections', function (Blueprint $table) {
            //
            $table->string('location_url')->nullable()->change();
            $table->string('lat')->nullable()->change();
            $table->string('long')->nullable()->change();
            $table->string('building_image')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('installation_location_detections', function (Blueprint $table) {
            //
            $table->string('location_url')->nullable(false)->change();
            $table->string('lat')->nullable(false)->change();
            $table->string('long')->nullable(false)->change();
            $table->string('building_image')->nullable(false)->change();
        });
    }
}
