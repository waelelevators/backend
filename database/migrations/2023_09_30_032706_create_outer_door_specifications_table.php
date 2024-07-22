<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outer_door_specifications', function (Blueprint $table) {
            $table->id();
            $table->integer('contract_id');
            $table->string('floor');
            $table->integer('number_of_doors');
            $table->string('out_door_specification');
            $table->string('door_opening_direction');
            $table->string('out_door_specification_tow');
            $table->string('door_opening_direction_tow');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('outer_door_specifications');
    }
};
