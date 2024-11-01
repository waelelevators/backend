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
<<<<<<< HEAD
            //
            $table->text('notes')->nullable(); // Adjust 'some_column' as needed
=======
            // Check if the 'notes' column exists before adding it
            if (!Schema::hasColumn('cabin_manufactures', 'notes')) {
                $table->text('notes')->nullable();
            }
>>>>>>> 1ebb111 (Maintenance Part)
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
<<<<<<< HEAD
            //
            $table->dropColumn('notes');
=======
            // Drop the 'notes' column if it exists
            if (Schema::hasColumn('cabin_manufactures', 'notes')) {
                $table->dropColumn('notes');
            }
>>>>>>> 1ebb111 (Maintenance Part)
        });
    }
}
