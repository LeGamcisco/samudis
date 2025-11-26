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
        Schema::create('parameters', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('stack_id')->default(0)->index('parameters_stack_id');
            $table->integer('parameter_id')->default(0)->index('parameters_parameter_id');
            $table->string('sispek_code')->nullable()->index('parameters_sispek_code');
            $table->string('ews_code')->nullable();
            $table->string('name', 100)->nullable();
            $table->string('caption', 100)->nullable();
            $table->integer('status_id')->default(0)->index('parameters_status_id');
            $table->string('p_type', 20)->nullable();
            $table->integer('unit_id')->default(0)->index('parameters_unit_id');
            $table->float('molecular_mass', 0, 0)->default(0)->index('parameters_molecular_mass');
            $table->string('formula')->nullable()->index('parameters_formula');
            $table->smallInteger('is_view')->default(0)->index('parameters_is_view');
            $table->smallInteger('is_graph')->default(0)->index('parameters_is_graph');
            $table->float('max_value', 0, 0)->default(0)->index('parameters_max_value');
            $table->integer('rounding')->nullable()->default(1);
            $table->integer('ain')->nullable();
            $table->string('ip_analyzer')->nullable();
            $table->string('analyzer_ip')->nullable();
            $table->smallInteger("is_priority")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parameters');
    }
};
