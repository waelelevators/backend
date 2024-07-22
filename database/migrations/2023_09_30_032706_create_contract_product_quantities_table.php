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
        Schema::create('contract_product_quantities', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('product_id');
            $table->string('price', 20);
            $table->integer('qty');
            $table->integer('elevator_type_id');
            $table->integer('floor')->default(1);
            $table->integer('stage')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_product_quantities');
    }
};
