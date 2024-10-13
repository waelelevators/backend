<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequiredProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('required_products', function (Blueprint $table) {
            $table->id();
            $table->morphs('productable');
            $table->integer('product_id');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('tax', 10, 2);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->string('status');
            $table->string('notes')->nullable();

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
        Schema::dropIfExists('required_products');
    }
}