<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToInstallationLocationDetectionsTable extends Migration
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
            $table->unsignedBigInteger('region_id');  // Foreign key column
            $table->foreign('region_id')->references('id')->on('regions');

            $table->unsignedBigInteger('city_id');  // Foreign key column
            $table->foreign('city_id')->references('id')->on('cities');

            $table->unsignedBigInteger('neighborhood_id');  // Foreign key column
            $table->foreign('neighborhood_id')->references('id')->on('neighborhoods');

            $table->string('building_image');
            $table->string('location_url');
            $table->decimal('lat');
            $table->decimal('long');
          
        });
    }

    /*
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('installation_location_detections', function (Blueprint $table) {
            //
            $table->dropColumn([
                'region_id',
                'city_id',
                'neighborhood_id',
                'building_image',
                'location_url',
                'lat',
                'long'
            ]);
        });
    }
}
