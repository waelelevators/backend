<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
<<<<<<< HEAD


            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('user_id')->after('attachments');

            $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
            // Assuming you have a users table and you want to set up a foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
=======
            // Check if 'contract_id' column doesn't exist before adding it
            if (!Schema::hasColumn('payments', 'contract_id')) {
                $table->unsignedBigInteger('contract_id');
                $table->foreign('contract_id')->references('id')->on('contracts')->onDelete('cascade');
            }

            // Check if 'user_id' column doesn't exist before adding it
            if (!Schema::hasColumn('payments', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('attachments');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
>>>>>>> c4980aa6f1d813202d514b551d0dd13643970ca7
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
<<<<<<< HEAD
            //
=======
            // Dropping foreign keys first
            if (Schema::hasColumn('payments', 'contract_id')) {
                $table->dropForeign(['contract_id']);
                $table->dropColumn('contract_id');
            }

            if (Schema::hasColumn('payments', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
>>>>>>> c4980aa6f1d813202d514b551d0dd13643970ca7
        });
    }
}
