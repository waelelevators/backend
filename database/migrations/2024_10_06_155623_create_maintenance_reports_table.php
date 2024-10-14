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
            $table->integer('maintenance_contract_id');
            $table->string('status')->default('created');
            $table->json('problems');
            $table->decimal('tax', 8, 2)->default(0);
            $table->decimal('price_without_tax', 10, 2)->default(0);
            $table->decimal('discount', 8, 2)->default(0);
            $table->decimal('final_price', 10, 2)->default(0);
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
