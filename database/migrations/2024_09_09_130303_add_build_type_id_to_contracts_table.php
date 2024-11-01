<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBuildTypeIdToContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('build_type_id');
            // $table->foreignId('build_type_id')->references('id')->on('building_types');
        });
    }

    /**
     * Reverse the migrations.
     *c
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            //
            $table->dropForeign(['build_type_id']);
            // $table->dropColumn('build_type_id');
        });
    }
}
