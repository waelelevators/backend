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
        Schema::create('rfq_responses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('rfq_id');
            $table->integer('supplier_id');
            $table->integer('rfq_line_item_id');
            $table->string('price');
            $table->string('note', 250)->nullable();
            $table->integer('product_id');
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
        Schema::dropIfExists('rfq_responses');
    }
};
