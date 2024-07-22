<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNeedToInternalDoorToElevatorTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('elevator_types', function (Blueprint $table) {
            //
            $table->boolean('need_to_internal_door')->default(false);
            $table->unsignedBigInteger('user_id')->default(1);
           
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('elevator_types', function (Blueprint $table) {
            //
            $table->dropColumn('need_to_internal_door');
        });
    }
}
