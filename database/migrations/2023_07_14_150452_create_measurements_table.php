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
        Schema::create('measurements', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parameter_id')->default(0)->index('measurements_parameter_id');
            $table->integer('data_status_id')->default(0)->index('measurements_data_status_id');
            $table->timestamp('time_group')->nullable()->index('measurements_time_group');
            $table->timestamp('measured_at')->nullable()->index('measurements_measured_at');
            $table->float('value', 0, 0)->default(0)->index('measurements_value');
            $table->float('value_correction', 0, 0)->default(0)->index('measurements_value_correction');
            $table->integer('unit_id')->default(0)->index('measurements_unit_id');
            $table->integer('validation_id')->default(0)->index('measurements_validation_id');
            $table->integer('condition_id')->default(0)->index('measurements_condition_id');
            $table->smallInteger('is_sent_cloud')->default(0)->index('measurements_is_sent_cloud');
            $table->string('sent_cloud_type', 10)->nullable();
            $table->string('sent_cloud_by', 100)->nullable();
            $table->timestamp('sent_cloud_at')->nullable();
            $table->integer('sent_cloud_tries')->default(0);
            $table->smallInteger('is_sent_klhk')->default(0)->index('measurements_is_sent_klhk');
            $table->string('sent_klhk_type', 10)->nullable();
            $table->string('sent_klhk_by', 100)->nullable();
            $table->timestamp('sent_klhk_at')->nullable();
            $table->integer('sent_klhk_tries')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 100)->default('');
            $table->string('created_ip', 20)->default('');
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 100)->default('');
            $table->string('updated_ip', 20)->default('');
            $table->smallInteger('is_deleted')->default(0);
            $table->softDeletes();
            $table->string('deleted_by', 100)->default('');
            $table->string('deleted_ip', 20)->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('measurements');
    }
};
