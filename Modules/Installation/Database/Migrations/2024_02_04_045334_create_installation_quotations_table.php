<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallationQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installation_quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id');
            $table->string('project_name')->nullable();
            $table->string('q_number')->nullable();
            $table->json('location_data');
            $table->json('elevator_data');
            $table->json('installments');
            $table->integer('status')->default(0);
            $table->integer('template_id')->default(0);
            $table->string('more_adds')->nullable();
            $table->text('notes')->nullable();
            $table->float('total_price');
            $table->float('discount');
            $table->float('tax');
            $table->integer('how_did_you_get_to_us');
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
        Schema::dropIfExists('installation_quotations');
    }
}
