<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultValueToStatusInRfqSupplierLineItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rfq_supplier_line_items', function (Blueprint $table) {
            //
            $table->string('status')->default('pending')->change(); // Set the default value for the status column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rfq_supplier_line_items', function (Blueprint $table) {
            //
            $table->integer('status')->default(null)->change();
        });
    }
}
