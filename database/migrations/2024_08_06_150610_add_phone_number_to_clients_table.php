<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhoneNumberToClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('data');

            $table->string('name')->after('type');
            $table->string('owner_name')->after('type')->nullable();
            $table->string('first_name')->after('type')->nullable();
            $table->string('second_name')->after('type')->nullable();
            $table->string('third_name')->after('type')->nullable();
            $table->string('last_name')->after('type')->nullable();
            $table->integer('phone')->after('type');
            $table->integer('phone2')->after('type')->nullable();
            $table->integer('whatsapp')->after('type')->nullable();
            $table->bigInteger('id_number')->after('type')->nullable();
            $table->bigInteger('tax_number')->after('type')->nullable();

            $table->unique(['id_number', 'type']);
            $table->unique([ 'type', 'phone']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            //
            $table->json('data');

            $table->dropColumn('name');
            $table->dropColumn('phone');
            $table->dropColumn('phone2');
            $table->dropColumn('whatsapp');
            $table->dropColumn('id_number');
            $table->dropColumn('tax_number');
            $table->dropColumn('owner_name');

            $table->dropColumn('first_name');
            $table->dropColumn('second_name');
            $table->dropColumn('third_name');
            $table->dropColumn('last_name');
        });
    }
}
