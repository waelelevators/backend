<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHowDidYouGetToUsToRepresentativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('representatives', function (Blueprint $table) {
            //
            $table->integer('how_did_you_get_to_us')->nullable()->after('id');
            // Replace 'column_name' with the column after which you want to add the new column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('representatives', function (Blueprint $table) {
            //
            $table->dropColumn('how_did_you_get_to_us');
        });
    }
}
