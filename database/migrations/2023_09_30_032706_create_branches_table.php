<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->integer('id', true);
<<<<<<< HEAD
            $table->string('name', 250);
            $table->string('prefix', 20);
            $table->integer('last_id')->default(1);
=======
            $table->string('name', length: 250);
            $table->string('prefix', 20);
            $table->integer('last_id')->default(1);
            $table->integer('last_maintenance_id')->default(1);

>>>>>>> c4980aa6f1d813202d514b551d0dd13643970ca7
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('branches');
    }
};
