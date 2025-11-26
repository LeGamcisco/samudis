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
        Schema::create('labjack_value_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('labjack_value_id')->default(0)->index('labjack_value_histories_labjack_value_id');
            $table->float('data', 0, 0)->default(0)->index('labjack_value_histories_data');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('labjack_value_histories');
    }
};
