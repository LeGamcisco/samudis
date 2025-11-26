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
        Schema::create('value_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parameter_id')->nullable();
            $table->float('measured', 0, 0)->default(0);
            $table->float('corrective', 0, 0)->default(0);
            $table->timestamp('xtimestamp')->default('2022-12-05 21:32:34.252518');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('value_logs');
    }
};
