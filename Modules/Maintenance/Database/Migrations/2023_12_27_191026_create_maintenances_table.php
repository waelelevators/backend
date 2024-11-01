<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMaintenancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_info_id')->constrained()
                ->references('id')->on('maintenance_infos');
            $table->date('started_date');
            $table->date('ended_date');
            $table->integer('visits_number');
            $table->foreignId('m_type_id')->constrained()
                ->references('id')->on('maintenance_types');
            $table->decimal('cost')->default(0);
            $table->longText('visit_note')->nullable();
            $table->foreignId('m_status_id')->constrained()
                ->references('id')->on('maintenance_statuses');
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
        Schema::dropIfExists('maintenances');
    }
}
