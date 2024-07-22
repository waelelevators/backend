<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrdersProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_orders_products', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->foreignId('work_order_id')->constrained()->references('id')->on('work_orders');
            $table->integer('qty');
            $table->integer('received')->default('0');
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
        Schema::dropIfExists('work_orders_products');
    }
}
