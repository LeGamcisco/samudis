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
        Schema::create('sispek', function (Blueprint $table) {
            $table->increments('id');
            $table->string('server', 100)->nullable()->index('sispek_server');
            $table->string('app_id', 100)->nullable()->index('sispek_app_id');
            $table->string('app_secret', 100)->nullable()->index('sispek_app_secret');
            $table->string('api_get_token', 100)->nullable()->index('sispek_api_get_token');
            $table->string('api_get_kode_cerobong', 100)->nullable()->index('sispek_api_get_kode_cerobong');
            $table->string('api_get_parameter', 100)->nullable()->index('sispek_api_get_parameter');
            $table->string('api_post_data', 100)->nullable()->index('sispek_api_post_data');
            $table->text('api_response_kode_cerobong')->nullable()->index('sispek_api_response_kode_cerobong');
            $table->text('api_response_parameter')->nullable()->index('sispek_api_response_parameter');
            $table->text('token')->nullable()->index('sispek_token');
            $table->timestamp('token_expired')->nullable()->index('sispek_token_expired');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sispek');
    }
};
