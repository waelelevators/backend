<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalDoorManufacturersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_door_manufacturers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id');
            $table->foreignId('door_size_id')->constrained()
                ->references('id')->on('door_sizes');
            $table->string('notes');
            $table->string('order_attached');
            $table->dateTime('started_date');
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
        Schema::dropIfExists('external_door_manufacturers');
    }
}
