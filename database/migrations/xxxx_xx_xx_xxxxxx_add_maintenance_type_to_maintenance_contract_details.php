<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaintenanceTypeToMaintenanceContractDetails extends Migration
{
    public function up()
    {
        Schema::table('maintenance_contract_details', function (Blueprint $table) {
            $table->string('maintenance_type')->default('free')->after('maintenance_contract_id');
        });
    }

    public function down()
    {
        Schema::table('maintenance_contract_details', function (Blueprint $table) {
            $table->dropColumn('maintenance_type');
        });
    }
}
