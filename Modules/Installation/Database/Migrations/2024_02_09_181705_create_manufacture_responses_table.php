<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManufactureResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manufacture_responses', function (Blueprint $table) {
            $table->id();
            $table->integer('m_id');
            $table->dateTime('accept_time');
            $table->dateTime('ended_time')->nullable();
            $table->enum('type', ['Internal', 'External', 'Cabin']);
            $table->foreignId('accepted_by')->references('id')->on('users');
            $table->foreignId('ended_by')->nullable()->references('id')->on('users');
            $table->foreignId('user_id')->constrained();
            $table->unique(['m_id', 'type']);
            $table->timestamps();
        });
    }
    // $table->foreignId('accepted_by')->constrained();
    // $table->foreignId('ended_by')->constrained()->nullable();
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manufacture_responses');
    }
}
