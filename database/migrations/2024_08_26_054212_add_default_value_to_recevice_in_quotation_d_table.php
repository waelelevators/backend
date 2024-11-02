<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultValueToReceviceInQuotationDTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotation_d', function (Blueprint $table) {
            //
            $table->integer('received')->default(0);
            $table->unsignedBigInteger('rfq_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotation_d', function (Blueprint $table) {
            //
            $table->integer('received')->default(null)->change();
            $table->unsignedBigInteger('rfq_id')->nullable(false)->change();
        });
    }
}
