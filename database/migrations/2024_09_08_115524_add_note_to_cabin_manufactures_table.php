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
        Schema::table('cabin_manufactures', function (Blueprint $table) {
<<<<<<< HEAD
            //
            $table->dropColumn('notes');
=======
            // Drop the 'notes' column if it exists
            if (Schema::hasColumn('cabin_manufactures', 'notes')) {
                $table->dropColumn('notes');
            }
>>>>>>> c4980aa6f1d813202d514b551d0dd13643970ca7
        });
    }
}
