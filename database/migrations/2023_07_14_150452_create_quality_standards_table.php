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
        Schema::create('quality_standards', function (Blueprint $table) {
            $table->increments('id');
            $table->float('value', 0, 0)->default(0);
            $table->integer('unit_id')->default(0)->index('quality_standards_unit_id');
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
        Schema::dropIfExists('quality_standards');
    }
};
