<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHandOverItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hand_over_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_responses_id')->references('id')->on('manufacture_responses');
            $table->foreignId('work_order_id')->references('id')->on('work_orders');
            $table->foreignId('employee_id')->references('id')->on('employees');
            $table->json('item_data');
            $table->foreignId('user_id');
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
        Schema::dropIfExists('hand_over_items');
    }
}
