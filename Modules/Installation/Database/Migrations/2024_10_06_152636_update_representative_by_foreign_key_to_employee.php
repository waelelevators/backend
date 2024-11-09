<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRepresentativeByForeignKeyToEmployee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('location_assignments_logs', function (Blueprint $table) {
            // Drop the old foreign key constraint
            $table->dropForeign(['representative_by']);

            // Add the new foreign key referencing the employees table
            $table->foreign('representative_by')->references('id')->on('employees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('location_assignments_logs', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['representative_by']);

            // Restore the old foreign key referencing the users table
            $table->foreign('representative_by')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
