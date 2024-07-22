<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepresentativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('representatives', function (Blueprint $table) {
            $table->id();
            $table->string('representativeable_type')->default('0');
            $table->string('representativeable_id');
            $table->string('contract_id');
            $table->string('name')->nullable();
            $table->enum('contract_type', [
                'installments', 'maintenances', 'main-quotations', 'inst-quotations', 'main-locations', 'inst-locations'
            ]);
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
        Schema::dropIfExists('representatives');
    }
}
