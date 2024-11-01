<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('location_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('l_assignment_id')->constrained()->references('id')->on('location_assignments');
            $table->json('location_data');
            $table->integer('status');
            $table->string('notes');
            $table->foreignId('detection_by')->constrained()->references('id')->on('users');
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
        Schema::dropIfExists('location_statuses');
    }
}
