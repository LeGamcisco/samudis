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
        Schema::create('configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('egateway_code', 50)->nullable()->index('configurations_egateway_code');
            $table->string('customer_name', 100)->nullable();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('lon', 20)->nullable();
            $table->string('lat', 20)->nullable();
            $table->integer('interval_request')->default(0)->index('configurations_interval_request');
            $table->integer('interval_sending')->default(0)->index('configurations_interval_sending');
            $table->integer('interval_retry')->default(0)->index('configurations_interval_retry');
            $table->integer('interval_das_logs')->default(0)->index('configurations_interval_das_logs');
            $table->integer('interval_average')->default(0)->index('configurations_interval_average');
            $table->integer('delay_sending')->default(0)->index('configurations_delay_sending');
            $table->integer('day_backup')->default(1);
            $table->integer('manual_backup')->default(0);
            $table->string('main_path');
            $table->string('mysql_path');
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
        Schema::dropIfExists('configurations');
    }
};
