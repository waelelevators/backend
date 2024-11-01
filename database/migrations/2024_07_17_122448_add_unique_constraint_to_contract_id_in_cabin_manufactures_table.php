<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
<<<<<<< HEAD
=======
use Illuminate\Support\Facades\DB;
>>>>>>> 1ebb111 (Maintenance Part)

class AddUniqueConstraintToContractIdInCabinManufacturesTable extends Migration
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
            $table->unique('contract_id');
=======
            // Check if the unique index already exists before adding it
            $uniqueConstraint = DB::select("SHOW INDEX FROM cabin_manufactures WHERE Key_name = 'cabin_manufactures_contract_id_unique'");

            if (empty($uniqueConstraint)) {
                // Add unique constraint if it doesn't exist
                $table->unique('contract_id');
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
            $table->dropUnique(['contract_id']);
=======
            // Check if the unique index exists before dropping it
            $uniqueConstraint = DB::select("SHOW INDEX FROM cabin_manufactures WHERE Key_name = 'cabin_manufactures_contract_id_unique'");

            if (!empty($uniqueConstraint)) {
                // Drop the unique constraint if it exists
                $table->dropUnique(['contract_id']);
            }
>>>>>>> 1ebb111 (Maintenance Part)
        });
    }
}
