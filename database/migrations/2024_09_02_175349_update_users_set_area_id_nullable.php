<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersSetAreaIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('area_id')->nullable()->change();
            $table->string('email')->unique()->change();

            // $table->unsignedBigInteger('rule_category_id')->change();
            //    $table->foreign('rule_category_id')->references('id')->on('rule_categories')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('area_id')->nullable(false)->change();
            $table->dropUnique(['email']);
            //   $table->dropForeign(['rule_category_id']);
        });
    }
}
