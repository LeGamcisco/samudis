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
        Schema::create('alarm_configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('enable_email')->nullable()->default(0);
            $table->smallInteger('enable_telegram')->nullable()->default(0);
            $table->string('sent_from')->nullable();
            $table->text('sent_to')->nullable();
            $table->string('protocol')->nullable();
            $table->string('host')->nullable();
            $table->string('smtp_user')->nullable();
            $table->string('smtp_pass')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('timeout')->nullable();
            $table->string('telegram_chat_id')->nullable();
            $table->text('telegram_bot_token')->nullable();
            $table->timestamp('xtimestamp')->default('2022-12-05 21:32:34.258836');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('alarm_configurations');
    }
};
