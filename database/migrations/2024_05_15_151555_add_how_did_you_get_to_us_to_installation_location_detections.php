<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHowDidYouGetToUsToInstallationLocationDetections extends Migration
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
            $table->integer('how_did_you_get_to_us');
            $table->renameColumn('shoulder_type', 'well_type');
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
            $table->dropColumn('how_did_you_get_to_us');
        });
    }
}
