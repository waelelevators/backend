<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRfqResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rfq_responses', function (Blueprint $table) {


            $table->unsignedBigInteger('industries_id')->nullable()->after('rfq_id');

            // Set up the foreign key constraint
            $table->foreign('industries_id')->references('id')
                ->on('industries')->onDelete('cascade');

            // Remove the 'note' column
            $table->dropColumn('note');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rfq_responses', function (Blueprint $table) {
            // Add the 'note' column back
            $table->text('note')->nullable();

            // Drop the foreign key and the 'industries_id' column
            $table->dropForeign(['industries_id']);
        });
    }
}
