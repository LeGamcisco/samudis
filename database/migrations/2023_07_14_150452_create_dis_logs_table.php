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
        Schema::create('dis_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parameter_id')->default(0)->index('dis_logs_parameter_id');
            $table->integer('data_status_id')->default(0)->index('dis_logs_data_status_id');
            $table->integer('unit_id')->default(0)->index('dis_logs_unit_id');
            $table->integer('validation_id')->default(0)->index('dis_logs_validation_id');
            $table->integer('condition_id')->default(0)->index('dis_logs_condition_id');
            $table->integer('notification')->default(0);
            $table->timestamp('time_group')->nullable()->index('dis_logs_time_group');
            $table->timestamp('measured_at')->nullable()->index('dis_logs_measured_at');
            $table->float('value', 0, 0)->default(0)->index('dis_logs_value');
            $table->float('value_correction', 0, 0)->default(0)->index('dis_logs_value_correction');
            $table->timestamp('avg_time_group')->nullable()->index('dis_logs_avg_time_group');
            $table->smallInteger('is_averaged')->default(0)->index('dis_logs_is_averaged');
            $table->smallInteger('is_sent_cloud')->default(0);
            $table->timestamp('sent_cloud_at')->nullable();
            $table->string('sent_cloud_by', 100)->nullable();
            $table->smallInteger('is_sent_sispek')->default(0)->index('dis_logs_is_sent_sispek');
            $table->timestamp('sent_sispek_at')->nullable();
            $table->string('sent_sispek_by', 100)->nullable();
            $table->integer('notification_email')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dis_logs');
    }
};
