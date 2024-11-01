<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyMaintenancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monthly_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_id')->constrained()->references('id')->on('maintenances');
            $table->dateTime('started_date');
            $table->dateTime('ended_date');
            $table->date('visit_date');
            $table->json('visit_data');
            $table->foreignId('visit_status_id')->constrained()->references('id')->on('visit_statuses');
            $table->json('visit_images');
            $table->longText('notes')->nullable();
            $table->foreignId('tech_id')->constrained()->references('id')->on('users');
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
        Schema::dropIfExists('monthly_maintenances');
    }
}
