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
            $table->bigInteger('installation_contract_id')->unsigned();
            $table->string('customer_id');
            $table->string('user_id');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('visits_count')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->integer('remaining_visits')->nullable();
            $table->integer('cancellation_allowance')->nullable()->default(1); // يمكن للعميل الاعتذار عن الزياره مرات محدده فقط
            $table->string('payment_status')->nullable()->default('unpaid');
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