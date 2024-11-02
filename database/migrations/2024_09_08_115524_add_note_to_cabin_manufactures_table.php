<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNoteToCabinManufacturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cabin_manufactures', function (Blueprint $table) {
            // Check if the 'notes' column exists before adding it
            if (!Schema::hasColumn('cabin_manufactures', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cabin_manufactures', function (Blueprint $table) {
            // Drop the 'notes' column if it exists
            if (Schema::hasColumn('cabin_manufactures', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
}
