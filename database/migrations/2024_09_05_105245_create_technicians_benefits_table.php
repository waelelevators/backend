<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechniciansBenefitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technicians_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->references('id')->on('work_orders');
            $table->enum('type', ['Deposit', 'Withdraw']);
            $table->decimal('amount', 15, 2);
            $table->text('statement');
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
        Schema::dropIfExists('technicians_benefits');
    }
}
