<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaintenanceContractDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maintenance_contract_details', function (Blueprint $table) {

            $table->id();
            $table->integer('installation_contract_id');
            $table->integer('maintenance_contract_id');
            $table->string('client_id');
            $table->string('user_id');
            $table->string('start_date')->nullable();
            $table->string('end_date')->nullable();
            $table->string('visits_count')->nullable();
            $table->string('cost')->nullable();
            $table->text('notes')->nullable();
            $table->string('remaining_visits')->nullable()->default(0);
            $table->string('cancellation_allowance')->nullable()->default(1);
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->nullable();
            $table->string('receipt_attachment')->nullable();
            $table->string('contract_attachment')->nullable();
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
        Schema::dropIfExists('maintenance_contract_details');
    }
}