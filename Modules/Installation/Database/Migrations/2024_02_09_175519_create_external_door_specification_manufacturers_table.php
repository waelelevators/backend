<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalDoorSpecificationManufacturersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_door_specification_manufacturers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ex_do_ma_id')->constrained()
                ->references('id')->on('external_door_manufacturers');

            $table->foreignId('do_spec_id')->constrained()
                ->references('id')->on('outer_door_directions');
            $table->integer('doors_number')->default(1);
            $table->foreignId('door_cover_id')->constrained()
                ->references('id')->on('colors');
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
        Schema::dropIfExists('external_door_specification_manufacturers');
    }
}
