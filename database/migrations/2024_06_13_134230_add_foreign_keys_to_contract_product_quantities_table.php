<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToContractProductQuantitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contract_product_quantities', function (Blueprint $table) {

            // Ensure the columns are of type unsignedBigInteger
            $table->unsignedBigInteger('floor_id')->change();
            $table->unsignedBigInteger('elevator_type_id')->change();
            $table->unsignedBigInteger('stage_id')->change();

            // Add foreign key constraints
            $table->foreign('floor_id')
                ->references('id')
                ->on('stops_numbers')
                ->onDelete('cascade');

            $table->foreign('elevator_type_id')
                ->references('id')
                ->on('elevator_types')
                ->onDelete('cascade');

            $table->foreign('stage_id')
                ->references('id')
                ->on('stages')
                ->onDelete('cascade');
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
            // Drop foreign key constraints
            $table->dropForeign(['floor_id']);
            $table->dropForeign(['elevator_type_id']);
            $table->dropForeign(['stage_id']);

            // Optional: Revert the column types if necessary (example here assumes original type is integer)
            // $table->integer('floor_id')->change();
            // $table->integer('elevator_type_id')->change();
            // $table->integer('stage_id')->change();
        });
    }
}
