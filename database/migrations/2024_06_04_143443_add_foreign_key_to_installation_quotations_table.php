<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToInstallationQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('installation_quotations', function (Blueprint $table) {
            //
         //   $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade'); // Adjust the referenced table and column as needed
           // $table->foreign('template_id')->references('id')->on('templates')->onDelete('cascade'); // Adjust the referenced table and column as needed

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
          //  $table->dropForeign(['client_id']); // Adjust the column name as needed
     //       $table->dropForeign(['template_id']); // Adjust the column name as needed
        });
    }
}
