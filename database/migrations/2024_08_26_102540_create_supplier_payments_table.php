<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    //  'invoice_id',
    //  'supplier_id',
    //  'payment_amount',
    //  'user_id',
    //  'attached_file',
    public function up()
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();

            // $table->foreignId('invoice_id')->constrained()->references('id')->on('invoices');
            //  $table->foreignId('supplier_id')->constrained()->references('id')->on('suppliers');

            $table->decimal('payment_amount', 15, 2);
            $table->foreignId('user_id');
            $table->string('attached_file');

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
        Schema::dropIfExists('supplier_payments');
    }
}
