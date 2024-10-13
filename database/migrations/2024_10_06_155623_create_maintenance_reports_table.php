<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_contract_id')->constrained('maintenance_contracts');
            $table->string('status');
            $table->json('problems');
            $table->decimal('tax', 8, 2);
            $table->decimal('price_without_tax', 10, 2);
            $table->decimal('discount', 8, 2)->default(0);
            $table->decimal('final_price', 10, 2);
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
        Schema::dropIfExists('maintenance_reports');
    }
}