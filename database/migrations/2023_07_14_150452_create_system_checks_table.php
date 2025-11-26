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
        Schema::create('system_checks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('system')->nullable();
            $table->integer('status')->default(0)->index('system_checks_status');
            $table->timestamp('last_check_at')->nullable()->index('system_checks_last_check_at');
            $table->string('error_code', 10)->nullable()->index('system_checks_error_code');
            $table->string('action', 100)->nullable();
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
        Schema::dropIfExists('system_checks');
    }
};
