<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateMaintenanceInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('maintenance_infos', function (Blueprint $table) {
            $table->dropColumn('how_did_you_get_to_us');
            $table->unsignedBigInteger('contract_id')->nullable()->change();
            $table->unsignedBigInteger('representative_id')->after('id'); // Adjust the column position as needed
            $table->foreign('representative_id')->references('id')->on('representatives')->onDelete('cascade'); // Adjust the referenced table and column as needed

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('maintenance_infos', function (Blueprint $table) {

            $table->dropForeign(['representative_id']); // Adjust the column name as needed
            $table->dropColumn('representative_id'); // Adjust the column name as needed

            $table->unsignedBigInteger('contract_id')->nullable(false)->change();
            $table->integer('how_did_you_get_to_us');
        });
    }
}
