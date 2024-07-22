<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternalDoorManufacturersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internal_door_manufacturers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()
                ->references('id')->on('contracts');
            $table->integer('doors_number')->default(1);
            $table->integer('door_cover_id');
            $table->integer('door_size_id');
            $table->dateTime('started_date');
            $table->string('order_attached');
            $table->string('notes');
            $table->foreignId('status_id')->constrained();
            $table->foreignId('user_id')->constrained();
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
        Schema::dropIfExists('internal_door_manufacturers');
    }
}
