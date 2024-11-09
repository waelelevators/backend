<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropLocationDataFromInstallationLocationDetectionsTable extends Migration
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
            $table->dropColumn('location_data');
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
            $table->json('location_data'); // Restore the column if you roll back

        });
    }
}
