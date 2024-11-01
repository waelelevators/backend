<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyToContractProductQuantitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract_product_quantities', function (Blueprint $table) {
            //
            // $table->unsignedBigInteger('product_id')->after('id'); // Replace 'id' with the appropriate column

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            //     $table->foreign('elevator_type_id')->references('id')->on('elevator_types')->onDelete('cascade');
            // $table->foreign('floor')->references('id')->on('floors')->onDelete('cascade');
            // $table->foreign('stage')->references('id')->on('stages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contract_product_quantities', function (Blueprint $table) {
            //
            // $table->dropForeign(['product_id']);
            //      $table->dropForeign(['contract_id']);
            //  $table->dropForeign(['floor']);
            //$table->dropForeign(['stage']);

            // Drop the column if needed
            //  $table->dropColumn('contract_id');
        });
    }
}
