<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRepresentativeIdToInstallationQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('installation_quotations', function (Blueprint $table) {
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
        Schema::table('installation_quotations', function (Blueprint $table) {
            //
            Schema::table('installation_quotations', function (Blueprint $table) {
                $table->dropForeign(['representative_id']); // Adjust the column name as needed
                $table->dropColumn('representative_id'); // Adjust the column name as needed
          
            });
        });
    }
}
