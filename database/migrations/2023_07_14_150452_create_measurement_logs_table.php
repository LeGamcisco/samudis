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
        Schema::create('measurement_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parameter_id')->default(0)->index('measurement_logs_parameter_id');
            $table->float('value', 0, 0)->default(0)->index('measurement_logs_value');
            $table->float('voltage', 0, 0)->default(0)->index('measurement_logs_voltage');
            $table->integer('unit_id')->default(0)->index('measurement_logs_unit_id');
            $table->smallInteger('is_averaged')->default(0)->index('measurement_logs_is_averaged');
            $table->smallInteger('is_das_log')->default(0)->index('measurement_logs_is_das_log');
            $table->timestamp('xtimestamp');
            $table->float('corrective', 0, 0)->nullable()->default(0);
            $table->integer('is_direct_plc')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('measurement_logs');
    }
};
