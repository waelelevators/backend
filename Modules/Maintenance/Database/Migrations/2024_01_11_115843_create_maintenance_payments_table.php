<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenancePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // $table->foreignId('m_id')->constrained()
        //         ->references('id')->on('maintenance_infos');

        Schema::create('maintenance_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_id')->constrained()->references('id')->on('maintenances');
            $table->decimal('amount');
            $table->string('attachment');
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
        Schema::dropIfExists('maintenance_payments');
    }
}
