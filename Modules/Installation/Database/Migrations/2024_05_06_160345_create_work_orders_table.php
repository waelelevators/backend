<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stage_id')->constrained()->references('id')->on('stages');
            $table->foreignId('assignment_id')->constrained()->references('id')->on('location_statuses');
            $table->string('status_id')->default('pending');
            $table->foreignId('user_id')->constrained()->references('id')->on('users');
            $table->string('manager_approval')->default('pending');
            $table->integer('products')->default(0);
            $table->string('start_at')->nullable();
            $table->string('end_at')->nullable();
            $table->string('duration')->nullable();
            $table->integer('freeze');
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
        Schema::dropIfExists('work_orders');
    }
}
