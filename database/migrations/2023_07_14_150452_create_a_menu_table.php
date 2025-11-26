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
        Schema::create('a_menu', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seqno')->default(0)->index('a_menu_seqno');
            $table->integer('parent_id')->default(0)->index('a_menu_parent_id');
            $table->string('name', 100)->nullable();
            $table->string('url')->nullable()->index('a_menu_url');
            $table->string('icon', 100)->nullable()->index('a_menu_icon');
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
        Schema::dropIfExists('a_menu');
    }
};
