<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMalfunctionResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('malfunction_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mal_id')->constrained()->references('id')->on('malfunctions');
            $table->string('mal_type_id');
            $table->json('malfunction_images');
            $table->json('malfunction_videos');
            $table->foreignId('status_id')->constrained()
                ->references('id')->on('malfunction_statuses');
            $table->integer('is_need_parts')->default(0);
            $table->json('elevators_parts');
            $table->string('notes')->nullable();
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
        Schema::dropIfExists('malfunction_responses');
    }
}
