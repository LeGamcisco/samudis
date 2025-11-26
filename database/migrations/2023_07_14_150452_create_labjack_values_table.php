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
        Schema::create('labjack_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('labjack_id')->default(0)->index('labjack_values_labjack_id');
            $table->integer('ain_id')->default(0)->index('labjack_values_ain_id');
            $table->float('data', 0, 0)->default(0)->index('labjack_values_data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('labjack_values');
    }
};
